<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * @package   Jagger
 * @author    Middleware Team HEAnet
 * @author    Janusz Ulanowski <janusz.ulanowski@heanet.ie>
 * @copyright Copyright (c) 2012, HEAnet Limited (http://www.heanet.ie)
 * @license   MIT http://www.opensource.org/licenses/mit-license.php
 */
class Approval
{

    protected $ci;
    protected $em;
    /**
     * @var \models\User $user
     */
    protected $user;

    public function __construct() {
        $this->ci = &get_instance();
        $this->em = $this->ci->doctrine->em;
        $this->user = $this->em->getRepository("models\User")->findOneBy(array('username' => $this->ci->session->userdata('username')));

    }

    /**
     * @param $obj
     * @param $action
     * @return \models\Queue
     */
    public function addToQueue($obj, $action) {
        log_message('debug', __METHOD__ . ': obj: ' . get_class($obj) . ' , action: ' . $action);
        $queue = new models\Queue();
        if ($obj instanceof models\Federation) {
            $queue->addFederation($obj->convertToArray());
            /**
             * @todo decide if to verify action value
             */
            $queue->setAction($action);
            $queue->setName($obj->getName());
            $queue->setEmail($this->user->getEmail());
            $queue->setCreator($this->user);
            $queue->setToken();
        }
        return $queue;
    }

    /**
     * @param \models\Provider $provider
     * @param $newScopes
     * @return bool
     */
    public function applyForScopeChange(models\Provider $provider, $newScopes) {
        $queue = new models\Queue();
        $queue->setRecipient($provider->getId());
        $queue->setRecipientType('provider');
        $queue->setCreator($this->user);
        $queue->setName($provider->getEntityId());
        $queue->setEmail($this->user->getEmail());
        $queue->setConfirm(true);
        $queue->setAction('UPDATE');
        $queue->setType('Provider');
        $queue->setObjectType('n');
        $data = array(
            'orig' => $provider->getScopeFull(),
            'new' => $newScopes
        );
        $diffFound = false;
        foreach (array('idpsso','aa') as $v){
            $diff1 = array_diff_assoc($data['orig'][''.$v.''],$data['new'][''.$v.'']);
            $diff2 = array_diff_assoc($data['new'][''.$v.''],$data['orig'][''.$v.'']);
            if(count($diff1) > 0 || count($diff2)>0){
                $diffFound = true;
                break;
            }

        }

        $queue->setObject(array('scope'=>$data));
        $queue->setToken();
        if($diffFound) {
            $this->em->persist($queue);
            return true;
        }
        return false;
    }

    /**
     * @param \models\Coc $coc
     * @param \models\Provider $provider
     * @return \models\Queue
     */
    public function applyForEntityCategory(models\Coc $coc, models\Provider $provider) {
        $queue = new models\Queue();
        $queue->setRecipient($coc->getId());
        $queue->setRecipientType('entitycategory');
        $queue->setCreator($this->user);
        $queue->setName($provider->getEntityId());
        $queue->setEmail($this->user->getEmail());
        $queue->setConfirm(true);
        $queue->setAction('APPLY');
        $queue->setType('Provider');
        $queue->setObjectType('n');
        $queue->setObject(array());
        $queue->setToken();
        $this->em->persist($queue);
        return $queue;
    }

    /**
     * @param \models\Coc $coc
     * @param \models\Provider $provider
     * @return \models\Queue
     */
    public function applyForRegistrationPolicy(models\Coc $coc, models\Provider $provider) {
        $nQueue = new models\Queue();
        $nQueue->setRecipient($coc->getId());
        $nQueue->setRecipientType('regpolicy');
        $nQueue->setCreator($this->user);
        $nQueue->setName($provider->getEntityId());
        $nQueue->setEmail($this->user->getEmail());
        $nQueue->setConfirm(true);
        $nQueue->setAction('APPLY');
        $nQueue->setType('Provider');
        $nQueue->setObjectType('n');
        $nQueue->setObject(array());
        $nQueue->setToken();
        $this->em->persist($nQueue);
        return $nQueue;
    }

    /**
     * @param \models\Federation $federation
     * @param \models\Provider $obj
     * @param $action
     * @return \models\Queue
     */
    public function invitationProviderToQueue(models\Federation $federation, models\Provider $obj, $action) {
        $nQueue = new models\Queue();
        $nQueue->setRecipient($obj->getId());
        $nQueue->setRecipientType('provider');
        $nQueue->setCreator($this->user);
        $nQueue->setName($federation->getName());
        $nQueue->setEmail($this->user->getEmail());
        $fed = array('id' => $federation->getId(), 'name' => $federation->getName(), 'urn' => $federation->getUrn());
        if ($action === 'Join') {
            $nQueue->inviteProvider($fed);
        }
        $nQueue->setToken();
        $this->em->persist($nQueue);
        $this->em->flush();
        return $nQueue;
    }

    /**
     * @param \models\Federation $federation
     * @param \models\Provider $obj
     * @param $action
     * @return \models\Queue
     */
    public function removeProviderToQueue(models\Federation $federation, models\Provider $obj, $action) {
        $nQueue = new models\Queue();
        $nQueue->setRecipient($obj->getId());
        $nQueue->setRecipientType('provider');
        $nQueue->setCreator($this->user);
        $nQueue->setName($federation->getName());
        $nQueue->setEmail($this->user->getEmail());
        $fed = array('id' => $federation->getId(), 'name' => $federation->getName(), 'urn' => $federation->getUrn());
        if ($action === 'Leave') {
            $nQueue->leaveProvider($fed);
        }
        $nQueue->setToken();
        $this->em->persist($nQueue);
        $this->em->flush();
        return $nQueue;
    }

    /**
     * @param \models\Provider $provider
     * @param \models\Federation $obj
     * @param $action
     * @param null $message
     * @return \models\Queue
     */
    public function invitationFederationToQueue(models\Provider $provider, models\Federation $obj, $action, $message = null) {

        $nQueue = new models\Queue();
        $nQueue->setRecipient($obj->getId());
        $nQueue->setRecipientType('federation');
        $nQueue->setCreator($this->user);
        $providername = $provider->getName();
        if (empty($providername)) {
            $providername = $provider->getEntityId();
        }
        $nQueue->setName($providername);
        $nQueue->setEmail($this->user->getEmail());
        $prov = array('id' => $provider->getId(), 'name' => $providername, 'entityid' => $provider->getEntityId(), 'message' => '' . $message . '');
        if ($action === 'Join') {
            $nQueue->inviteFederation($prov);
        }
        $nQueue->setToken();
        $this->em->persist($nQueue);
        $this->em->flush();
        return $nQueue;
    }

    /**
     * @param \models\Federation $federation
     * @return \models\Queue
     */
    public function removeFederation(models\Federation $federation) {
        $nQueue = new models\Queue();
        $nQueue->setCreator($this->user);
        $nQueue->setName($federation->getName());
        $nQueue->setEmail($this->user->getEmail());
        $nQueue->setAction('Delete');
        $nQueue->addFederation($federation->convertToArray());
        $nQueue->setToken();
        return $nQueue;
    }

}

