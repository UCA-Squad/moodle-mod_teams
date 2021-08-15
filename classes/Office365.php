<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Microsoft\Graph\Http;

/**
 * Class Office365
 * Class which collect needed functions and API calls of the plugin.
 */
/**
 * Class Office365.
 *
 * The purpose of this class is to collect needed functions in the plugin and all API calls
 * to the Microsoft Graph API.
 *
 * @package    mod_teams
 * @copyright  2020 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.8
 */
class Office365
{
    private $tenantId;
    private $clientId;
    private $clientSecret;
    private $token;
    public $userUrlPrefix = "https://graph.microsoft.com/v1.0/users/";

    /**
     * Office365 constructor.
     * @param string $tenantId
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(string $tenantId,string $clientId, string $clientSecret)
    {
        $this->tenantId = $tenantId;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Get token needed by API.
     * @return mixed the token.
     * @throws Exception
     */
    public function getToken()
    {
        if (is_null($this->token)) {
            $this->token = $this->generateToken();
        }
        elseif ($this->token->expires_on <= time()) {
            $this->token = $this->generateToken();
        }

        return $this->token;
    }

    /**
     * Function to generated the token.
     * @return mixed
     * @throws Exception
     */
    public function generateToken()
    {
        $url = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/token?api-version=1.0';

        if (WS_SERVER) {
            // Classic curl call if we call it throw web service (e.g Moodle mobile app calls).
            $curl = curl_init($url);
            $params = [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'resource' => 'https://graph.microsoft.com/',
                'grant_type' => 'client_credentials',
            ];
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            if (get_config('moodle', 'proxyhost')) {
                // Use defined web proxy.
                curl_setopt($curl, CURLOPT_PROXY, get_config('moodle', 'proxyhost'));
            }
            $result = curl_exec($curl);
            return json_decode($result);
        } else {
            // Web calls, we choose to use Guzzle in these cases.
            try {
                $guzzle = new \GuzzleHttp\Client();
                $params = ['form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'resource' => 'https://graph.microsoft.com/',
                    'grant_type' => 'client_credentials',
                ]
                ];
                if (get_config('moodle', 'proxyhost')) {
                    // Use defined web proxy.
                    $params['form_params']['proxy'] = get_config('moodle', 'proxyhost');
                }

                $token = json_decode($guzzle->post($url, $params)->getBody()->getContents());
                return $token;
            } catch (Exception $exception) {
                throw $exception;
            }
        }
    }

    /**
     * Function to get the Graph API object used to do the API calls.
     * @param string $version the API version to use.
     * @return Graph
     * @throws Exception
     */
    private function getGraphApi($version = "v1.0")
    {
        $graph = new Microsoft\Graph\Graph();
        $graph->setApiVersion($version);
        $graph->setAccessToken($this->getToken()->access_token);
        if (get_config('moodle', 'proxyhost')) {
            // Use defined web proxy.
            $graph->setProxyPort(get_config('moodle', 'proxyhost'));
        }

        return $graph;
    }

    /**
     * Function to get the azure user id from the email address given in parameters.
     * @param string $email the email address of the user.
     * @return string|null the id of the user or null if it has not been found. Throws an exception in case of error.
     */
    public function getUserId(string $email)
    {
        $queryParams = ['$filter' => "userPrincipalName eq '$email' or mail eq '$email'"];
        $url = '/users?' . http_build_query($queryParams);

        try {
            $graph = $this->getGraphApi();
            $user = $graph->createRequest("GET", $url)
                ->setReturnType(\Microsoft\Graph\Model\User::class)
                ->execute();

            if (count($user) > 1) {
                throw new Exception(get_string('notunique', 'mod_teams'));
            }

            return ($user[0]) ? $user[0]->getId() : null;
        }
        catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Function to create a team from the group id given in parameters.
     * @param string $groupId the source group id for the team.
     * @param array $memberSettings array of users ids to add as members.
     * @return Model\Group the team created.
     * @throws Exception
     */
    public function createTeam(string $groupId, array $memberSettings = [])
    {
        $graph = $this->getGraphApi();

        $parameters = ["memberSettings" => $memberSettings];
        if (empty($parameters["memberSettings"])) {
            $parameters["memberSettings"] = [
                "allowCreateUpdateChannels" => false,
                "allowDeleteChannels" => false,
                "allowAddRemoveApps" => false,
                "allowCreateUpdateRemoveTabs" => false,
                "allowCreateUpdateRemoveConnectors" => false
            ];
        }

        try {
            return $graph->createRequest("PUT", "/groups/$groupId/team")
                ->setReturnType(\Microsoft\Graph\Model\Group::class)
                ->attachBody(json_encode($parameters))
                ->execute();
        }
        catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Function to create a group with informations given in parameters.
     * @param string $name the name of the group.
     * @param string $description the description of the group.
     * @param array|string $ownersId array of users ids to add as this group owners.
     * @return Model\Group the created group.
     * @throws Exception
     */
    public function createGroup(string $name, string $description, $ownersId)
    {
        $users = [];
        foreach ((array) $ownersId as $ownerId) {
            $users[] = $this->userUrlPrefix . $ownerId;
        }

        $group = new \Microsoft\Graph\Model\Group();
        $group->setDisplayName($name);
        $group->setMailNickname(preg_replace("/[^A-Za-z0-9]/", '', $name).uniqid()); // Has to be unique
        $group->setDescription($description);
        $group->setVisibility("Private");
        $group->setGroupTypes(["Unified"]);
        $group->setMailEnabled(true);
        $group->setSecurityEnabled(false);
        $group->setOwners($users);
        $group->setMembers($users);

        $data = $group->jsonSerialize();

        $data["owners@odata.bind"] = $data["owners"];
        $data["members@odata.bind"] = $data["members"];
        $data["resourceBehaviorOptions"] = ["WelcomeEmailDisabled"];

        unset($data["owners"]);
        unset($data["members"]);

        $graph = $this->getGraphApi();

        try {
            $response = $graph->createRequest("POST", "/groups")
                ->attachBody($data)
                ->setReturnType(\Microsoft\Graph\Model\Group::class)
                ->execute();
           return $response->getId();
        }
        catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * "Getter" of a the team with groupid given in parameters.
     * @param $groupId the group id
     * @return Group the team or an exception in case of error in the request.
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function readTeam($groupId)
    {
        if (WS_SERVER) {
            // Classic curl call if we call it throw web service (e.g Moodle mobile app calls).
            $url = "https://graph.microsoft.com/v1.0/groups/$groupId/team";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Bearer ".$this->generateToken()->access_token]);
            if (get_config('moodle', 'proxyhost')) {
                // Use defined web proxy.
                curl_setopt($curl, CURLOPT_PROXY, get_config('moodle', 'proxyhost'));
            }
            $result = json_decode(curl_exec($curl));

            if ($result->error) {
                // We have an error => throw an exception to display its error message.
                throw new Exception('Teams not found');
            }

            return json_decode($result);

        } else {
            // Web calls.
            $url = "/groups/$groupId/team";
            $graph = $this->getGraphApi();

            return $graph->createRequest("GET", $url)
                ->setReturnType(\Microsoft\Graph\Model\Group::class)
                ->execute();
        }
    }

    /**
     * Function to create a new team from another (id given in parameters) and with own informations given in parameters.
     * This will copy a team specific format for example.
     * @param string $team id og the model team.
     * @param string $name the name of the new team to create.
     * @param string $description the description of the new team to create.
     * @param array $ownersId array of users ids to add as owners of the new team.
     * @return Model\Group the new created team.
     * @throws Exception
     */
    public function copyTeam(string $team, string $name,string $description, $ownersId)
    {
        $graph = $this->getGraphApi();
        $users = [];
        foreach ((array) $ownersId as $ownerId) {
            $users[] = $this->userUrlPrefix.$ownerId;
        }

        $group = new \Microsoft\Graph\Model\Group();
        $group->setDisplayName($name);
        $group->setMailNickname(preg_replace("/[^A-Za-z0-9]/", '', $name).uniqid()); // Has to be unique
        $group->setDescription($description);
        $group->setVisibility("Private");
//        $group->setGroupTypes(["Unified"]);
//        $group->setMailEnabled(true);
//        $group->setSecurityEnabled(false);
        $group->setOwners($users);
//        $group->setMembers($users);

        $data = $group->jsonSerialize();
        $data["partsToClone"] = "apps,tabs,settings,channels";
        $data["resourceBehaviorOptions"] = ["WelcomeEmailDisabled"];
//        $data["owners"] = $users;
//        $data["owners@odata.bind"] = $users;
//        $data["owners@odata.bind"] = $data["owners"];

        unset($data["owners"]);
//        unset($data["members"]);

        try {
            $response = $graph->createRequest("POST", "/teams/$team/clone")
                ->attachBody($data)
                ->execute();

            if ($response) {
                $location = $response->getHeaders()["Location"][0];
                $team_id = explode("'", explode("/", $location)[1])[1];
                $team = $this->readTeam($team_id);

                return $team;
            }
        }
        catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Function to update the group owners for the group given in parameters.
     * If a team model is used in your system, this function will also owners of the model team from the given group
     * @param string $group the group id
     * @param array|string $ownersId array of the users ids to add as group owners.
     * @return string
     * @throws Exception
     */
    public function updateGroupOwners($group, $ownersId)
    {
        global $USER;
        $current = $this->getUserId($USER->email);

        try {
            $response = $this->addOwner($current, $group, false);
            if ($response && $response->getStatus() == 204) {
                // Ok.
                if (get_config('mod_teams', 'team_model')) {
                    // If we use model team -> delete owners of this model team in the given team.
                    $this->deleteModelOwners($group, false, $current);
                }
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Function to add the user given in parameters to the group owners.
     * @param $ownerId the user id to add as owner.
     * @param $groupId the group id.
     * @param bool $retry bool to indicate if we want to retry action if it fails.
     * @return mixed
     */
    public function addOwner($ownerId, $groupId, $retry = false)
    {
        $graph = $this->getGraphApi();
        $data = json_encode(["@odata.id" => $this->userUrlPrefix . $ownerId]);

        try {
            return $graph->createRequest("POST", "/groups/" . $groupId . "/owners/\$ref")
                ->attachBody($data)
                ->execute();
        }
        catch (Exception $e) {
            if (!$retry && $e->getCode() == 404) {
                // If we have 404 error, we retry the action just one time in order to not take too many exec time.
                sleep(10);
                return $this->addOwner($ownerId, $groupId, true);
            }

            return null;
        }
    }

    /**
     * Function to delete owners of the group given in parameters.
     * @param $groupId the group id.
     * @param bool $retry bool to indicate if we want to retry action if it fails.
     * @param string|null $current the current user id or null (only delete other users).
     * @throws Exception
     */
    public function deleteModelOwners($groupId, $retry = false, $current = null)
    {
        global $USER;
        $current = ($current) ? $current : $this->getUserId($USER->email);
        $graph = $this->getGraphApi();

        try {
            $response = $graph->createRequest("GET", "/groups/" . get_config('mod_teams', 'team_model') . "/owners")
                ->execute();
            $model_owners = [];
            foreach ($response->getBody()["value"] as $owner) {
                $model_owners[] = $owner['id'];
            }

            foreach ($model_owners as $owner) {
                if ($owner != $current) {
                    $graph->createRequest("DELETE", "/groups/" . $groupId . "/owners/" . $owner . "/\$ref")
                        ->execute();
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Function to create a new online meeting.
     * @param string $userId the organizer id of the meeting.
     * @param string $subject the subject of the meeting.
     * @param $startDateTime the meeting start date.
     * @param $endDateTime the meeting end date.
     * @return Model\OnlineMeeting the created meeting.
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function createBroadcastEvent(string $subject, $startDateTime, $endDateTime, $user)
    {
        $event = new \Microsoft\Graph\Model\Event();
        $start = ($startDateTime > 0) ? new DateTime(date('Y-m-d\TH:i:s.0000000', $startDateTime)) : new DateTime('now');
        $event->setStart(["dateTime" => $start->format('Y-m-d\TH:i:s.0000000'), "timeZone" => timezone_name_get($start->getTimeZone())]);
        if ($endDateTime > 0) {
            $end = new DateTime(date('Y-m-d\TH:i:s.0000000', $endDateTime));
            $event->setEnd(["dateTime" => $end->format('Y-m-d\TH:i:s.0000000'), "timeZone" => timezone_name_get($end->getTimeZone())]);
        }
        else {
            // If close date is empty we use configuration setting to fix the end of the meeting to avoid confusions.
            $default_end = date('Y-m-d\TH:i:s.0000000', strtotime($start->format('Y-m-d\TH:i:s.0000000') . " " . get_config('mod_teams', 'meeting_default_duration')));
            $event->setEnd(['dateTime' => $default_end, 'timeZone' => timezone_name_get($start->getTimeZone())]);
        }
        $event->setSubject($subject);
        $event->setAllowNewTimeProposals(false);
        $recipient = new \Microsoft\Graph\Model\Recipient();
        $recipient->setEmailAddress(['name' => fullname($user), 'address' => $user->email]);
        $event->setOrganizer($recipient);
        $event->setIsOrganizer(true);
        $event->setType("singleInstance");
        $event->setResponseRequested(false);
        $event->setIsReminderOn(false);
        $event->setReminderMinutesBeforeStart(0);
        $attendee = new \Microsoft\Graph\Model\Attendee();
        $attendee->setEmailAddress(['name' => fullname($user), 'address' => $user->email]);
        $attendees = [$attendee];
        $event->setAttendees($attendees);
        $event->setIsOnlineMeeting(true);
        $event->setOnlineMeetingProvider("teamsForBusiness");
        $data = $event->jsonSerialize();

        $graph = $this->getGraphApi("beta");
        $email = strtolower($user->email);

        try {
            $response = $graph->createRequest("POST", "/users/$email/events")
                ->attachBody($data)
                ->setReturnType(\Microsoft\Graph\Model\Event::class)
                ->execute();
            return $response;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Update a broadcast event.
     * @param $event_id identifiant of the created event.
     * @param $datas form data.
     * @param $user user who created the event.
     * @return Http\GraphResponse|mixed
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function updateBroadcastEvent($event_id, $datas, $user)
    {
        $event = new \Microsoft\Graph\Model\Event();
        $event->setSubject(str_replace(get_string('meeting_prefix', 'mod_teams'), '', $datas->name));
        $start = ($datas->opendate > 0) ? new DateTime(date('Y-m-d\TH:i:s.0000000', $datas->opendate)) : new DateTime('now');
        $event->setStart(['dateTime' => $start->format('Y-m-d\TH:i:s.0000000'), 'timeZone' => timezone_name_get($start->getTimeZone())]);
        if ($datas->closedate > 0) {
            $end = new DateTime(date('Y-m-d\TH:i:s.0000000', $datas->closedate));
            $event->setEnd(['dateTime' => $end->format('Y-m-d\TH:i:s.0000000'), 'timeZone' => timezone_name_get($end->getTimeZone())]);
        }
        else {
            // If close date is empty we use configuration setting to fix the end of the meeting to avoid confusions.
            $default_end = date('Y-m-d\TH:i:s.0000000', strtotime($start->format('Y-m-d\TH:i:s.0000000') . " " . get_config('mod_teams', 'meeting_default_duration')));
            $event->setEnd(['dateTime' => $default_end, 'timeZone' => timezone_name_get($start->getTimeZone())]);
        }
        $data = $event->jsonSerialize();

        $graph = $this->getGraphApi("beta");
        $email = (!empty($datas->createforroom)) ? $datas->createforroom : strtolower($user->email);

        try {
            $response = $graph->createRequest("PATCH", "/users/$email/events/$event_id")
                ->attachBody($data)
                ->setReturnType(\Microsoft\Graph\Model\Event::class)
                ->execute();

            return $response;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Create an online meeting and generate the meeting link available since its creation.
     * @param string $userId the identifiant of the creator.
     * @param string $subject the subject of the reunion.
     * @return Http\GraphResponse|mixed
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function createOnlineMeeting(string $userId, string $subject)
    {
        $onlineMeeting = new \Microsoft\Graph\Model\OnlineMeeting();
        $onlineMeeting->setSubject($subject);
        $user = new \Microsoft\Graph\Model\Identity();
        $user->setId($userId);
        $identity = new \Microsoft\Graph\Model\IdentitySet();
        $identity->setUser($user->jsonSerialize());
        $participant = new \Microsoft\Graph\Model\MeetingParticipantInfo();
        $participant->setIdentity($identity->jsonSerialize());
        $participants = new \Microsoft\Graph\Model\MeetingParticipants();
        $participants->setOrganizer($participant->jsonSerialize());
        $onlineMeeting->setParticipants($participants->jsonSerialize());
        $data = $onlineMeeting->jsonSerialize();
        $lobbyBypassSettings = new \Microsoft\Graph\Model\Entity(["scope" => "everyone", "isDialInBypassEnabled" => true]);
        $data["lobbyBypassSettings"] = $lobbyBypassSettings->jsonSerialize();
        $data["autoAdmittedUsers"] = "everyone";
        $data["allowedPresenters"] = "organizer";
        $graph = $this->getGraphApi("beta");

        try {
            $response = $graph->createRequest("POST", "/communications/onlineMeetings")
                ->attachBody($data)
                ->setReturnType(\Microsoft\Graph\Model\OnlineMeeting::class)
                ->execute();
            return $response;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get the meeting.
     * @param $teams the moodle mod teams object.
     * @return bool|Http\GraphResponse|mixed
     * @throws \Microsoft\Graph\Exception\GraphException
     * @throws dml_exception
     */
    public function getMeetingObject($teams)
    {
        global $DB;
        if (!$teams->reuse_meeting) {
            $user = $DB->get_record('user', ['id' => $teams->creator_id]);
            return $this->getBroadcastEvent($user->email, $teams->resource_teams_id);
        }
        //@todo: check if functionnal.

        return null;
    }

    /**
     * "Getter" of the online meeting object.
     * @param string $meetingId the meeting id.
     * @return Http\GraphResponse|mixed
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function getOnlineMeeting(string $meetingId)
    {
        $queryParams = ['$filter' => "VideoTeleconferenceId eq '$meetingId'"];

        $url = '/communications/onlineMeetings/?' . http_build_query($queryParams);
        $graph = $this->getGraphApi();

        return $graph->createRequest("GET", $url)
            ->setReturnType(\Microsoft\Graph\Model\OnlineMeeting::class)
            ->execute();
    }

    /**
     * "Getter" of the meeting, with a given id.
     * @param string $user the user id.
     * @param string $event_id the meeting id.
     * @return Model\OnlineMeeting the meeting.
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function getBroadcastEvent($user, $event_id)
    {
        if (WS_SERVER) {
            // Classic curl call if we call it throw web service (e.g Moodle mobile app calls).
            $url = "https://graph.microsoft.com/v1.0/users/$user/events/$event_id";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Bearer ".$this->generateToken()->access_token]);
            if (get_config('moodle', 'proxyhost')) {
                // Use defined web proxy.
                curl_setopt($curl, CURLOPT_PROXY, get_config('moodle', 'proxyhost'));
            }
            $result = json_decode(curl_exec($curl));

            if ($result->error) {
                // We have an error => throw an exception to display its error message.
                throw new Exception('Online meeting not found');
            }

            return json_decode($result);
        } else {
            // Web calls.
            $graph = $this->getGraphApi();
            return $graph->createRequest("GET", "/users/$user/events/$event_id")
                ->setReturnType(\Microsoft\Graph\Model\Event::class)
                ->execute();
        }
    }

    /**
     * Function to delete the meeting with the id given in parameters.
     * @param string $meetingId the meeting id.
     * @return Http\GraphResponse|mixed
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function deleteOnlineMeeting(string $meetingId)
    {
        $url = '/communications/onlineMeetings/?' . $meetingId;
        $graph = $this->getGraphApi();

        try {
            return $graph->createRequest("DELETE", $url)
                ->execute();
        }
        catch (Exception $exception) {
            throw $exception;
        }
    }
}