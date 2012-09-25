<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * ResourceRegistry3
 * 
 * @package     RR3
 * @author      Middleware Team HEAnet 
 * @copyright   Copyright (c) 2012, HEAnet Limited (http://www.heanet.ie)
 * @license     MIT http://www.opensource.org/licenses/mit-license.php
 *  
 */

/**
 * MY_form_validation Class
 * 
 * @package     RR3
 * @subpackage  Libraries
 * @author      Janusz Ulanowski <janusz.ulanowski@heanet.ie>
 */

class MY_form_validation extends CI_form_validation {

    protected $em;

    function __construct()
    {
        parent::__construct();
        $this->em = $this->CI->doctrine->em;
    }

    /*
      function is_unique($attribute_name, $model, array $condition) {
      $cond = array_keys($condition);

      $ent = $this->em->getRepository("$model")->findOneBy($condition);
      if (!empty($ent)) {
      $this->set_message($attribute_name, "The %s : \"" . $condition[$cond[0]] . "\" does already exist in the system.");
      return FALSE;
      } else {
      return TRUE;
      }
      }
     */

    function alpha_dash_comma($str)
    {

        $result =  (bool) preg_match('/^[\s-_a-z0-9,\.\@]+$/i', $str);
       
        if($result === FALSE)
        {
            $this->set_message('alpha_dash_comma', "%s :  contains incorrect characters");
        }
        return $result;
    }

    /**
     *
     * @param type $homeorg
     * @return type boolean
     * 
     */
    function homeorg_unique($homeorg)
    {
        $ent = $this->em->getRepository("models\Provider")->findOneBy(array('name' => $homeorg));
        if (!empty($ent))
        {
            $this->set_message('homeorg_unique', "The %s : \"$homeorg\" does already exist in the system.");
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
  
    function entityid_unique_update($entityid,$id)
    {
         $ent = $this->em->getRepository("models\Provider")->findOneBy(array('entityid' => $entityid));
         if(!empty($ent))
         {
             if($id == $ent->getId())
             {
                return TRUE;
             }
             else
             {
                $this->set_message('entityid_unique_update', "The %s \"$entityid\" does belong to other provider");
                return FALSE;
             }
         }
         else
         {
             return TRUE;
         }
    }

    function ssohandler_unique($handler)
    {
        $ent = $this->em->getRepository("models\ServiceLocation")->findOneBy(array('url' => $handler));
        if (!empty($ent))
        {
            $this->set_message('ssohandler_unique', "The %s : \"$handler\" does already exist in the system.");
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    function entity_unique($entity)
    {
        $ent = $this->em->getRepository("models\Provider")->findOneBy(array('entityid' => $entity));
        if (!empty($ent))
        {
            $this->set_message('entity_unique', "The %s : \"$entity\" does already exist in the system.");
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    function user_mail_unique($email)
    {
        $u = $this->em->getRepository("models\User")->findOneBy(array('email' => $email));
        if (!empty($u))
        {
            $this->set_message('user_mail_unique', "The %s : \"$email\" does already exist in the system.");
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    function user_username_unique($username)
    {
        $u = $this->em->getRepository("models\User")->findOneBy(array('username' => $username));
        if (!empty($u))
        {
            $this->set_message('user_username_unique', "The %s : \"$username\" does already exist in the system.");
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    function valid_requirement_attr($req)
    {
        if($req == 'required' or $req == 'desired')
        {
            return TRUE;
        }
        else
        {
            $this->set_message('valid_requirement_attr', "Invalid value injected in requirement");
            return FALSE;
        }
    }

    function user_username_exists($username)
    {
        $u = $this->em->getRepository("models\User")->findOneBy(array('username' => $username));
        if (empty($u))
        {
            $this->set_message('user_username_exists', "The %s : \"$username\" does not exist in the system.");
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    function verify_cert($cert)
    {
        $i = explode("\n", $cert);
        $c = count($i);
        if ($c < 2)
        {
            $pem = chunk_split($cert, 64, "\n");
            $cert = $pem;
        }
        $this->CI->load->helper('cert');
        $ncert = getPEM($cert);
        $res = openssl_x509_parse($ncert);
        if (is_array($res))
        {
            return TRUE;
        }
        else
        {
            $this->set_message('verify_cert', "The %s : is not valid x509 cert.");
            return FALSE;
        }
    }

    function valid_url($url)
    {

        $result = preg_match('|^http(s)?://[a-z0-9-]+(.[~a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
        if (!$result)
        {
            $this->set_message('valid_url', "The %s : is not valid URL.");
            return FALSE;
        }

        return TRUE;
    }

    function acs_index_check($acs_index)
    {
        $result = true;
        if (!empty($acs_index) && is_array($acs_index))
        {
            $count = count($acs_index);
            foreach ($acs_index as $key => $value)
            {
                if (($key != 'n' && !isset($value)) or $value < 0)
                {
                    $this->set_message('acs_index_check', "incorrect or no value in one of  \"%s\" " . $key . " " . $value);
                    return false;
                }
            }

            $acs_index_uniq = array_unique($acs_index);
            $count2 = count($acs_index_uniq);

            if ($count != $count2)
            {
                $this->set_message('acs_index_check', "Found duplicated values in \"%s\"");
                $result = false;
            }
        }

        return $result;
    }

    function array_valid_url($urls_in_array)
    {
        return true;
    }

    function setup_allowed()
    {
        $x = $this->em->getRepository("models\User")->findAll();
        $count_x = count($x);
        if ($count_x > 0)
        {
            $this->set_message('setup_allowed', "Database is not empty, you cannot initialize setup");
            return FALSE;
        }
        else
        {
            return true;
        }
    }
    function valid_static($usage, $t_metadata_entity)
    {
        $tmp_array=array();
        $tmp_array=explode(':::',$t_metadata_entity);
        
        $compared_entityid  = "";
        if(array_key_exists('1',$tmp_array))
        {
            $compared_entityid  = trim($tmp_array[1]);
        }
        $is_used = $usage;
        $t_metadata = $tmp_array[0];
        $metadata = trim(base64_decode($t_metadata));





        log_message('debug', '---- validation static metadata ------');
        log_message('debug', 'is_used::' . $is_used);
        log_message('debug', 'metadata::' . $metadata);
        log_message('debug', 'entityid::' . $compared_entityid);
        if (empty($metadata))
        {
            log_message('debug', 'metadata --- empty');
        }
        else
        {
            log_message('debug', 'metadata --- not empty:');
        }
        $result = false;
        if (empty($metadata) && !empty($is_used))
        {
            log_message('debug', 'valid_static: result:: invalid metadata');
            $this->set_message('valid_static', "The %s : is empty.");
            return $result;
        }
        libxml_use_internal_errors(true);
         $this->CI->load->library('metadata_validator');
         $xmls = simplexml_load_string($metadata);
         if(!empty($xmls))
         {
		//$docxml = new \DomDocument();
                //$docxml->loadXML($metadata);
               	$docxml = new \DomDocument();
        	libxml_use_internal_errors(true);
		$docxml->loadXML($metadata);
		$xpath = new \DomXPath($docxml);
                $xpath->registerNamespace('md', 'urn:oasis:names:tc:SAML:2.0:metadata');
		$xpath->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
            	$xpath->registerNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:assertion');
            	$xpath->registerNamespace('shibmd', 'urn:mace:shibboleth:metadata:1.0');
            	$xpath->registerNamespace('mdui', 'urn:oasis:names:tc:SAML:metadata:ui');
            	$xpath->registerNamespace('mdrpi', 'urn:oasis:names:tc:SAML:metadata:rpi');

                $first_attempt = $this->CI->metadata_validator->validateWithSchema($metadata);
                if(empty($first_attempt))
                {
			$tmp_metadata = $docxml->saveXML();
                        //log_message('debug',$tmp_metadata);
                        $second_attempt = $this->CI->metadata_validator->validateWithSchema($tmp_metadata);
                        if(!empty($second_attempt))
                        {
                            $result = TRUE;
                        }
                        else
                        {
                           $err_details = "<br />Make sure elements contains namespaces ex. md:EntityDescriptor.";
                           $err_details .='<br />Also inside EntitiyDescriptor element you must declare namespaces defitions<br/> <code>xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata"  xmlns:shibmd="urn:mace:shibboleth:metadata:1.0" xmlns:mdui="urn:oasis:names:tc:SAML:metadata:ui" xmlns:mdrpi="urn:oasis:names:tc:SAML:metadata:rpi"  xmlns:ds="http://www.w3.org/2000/09/xmldsig#"</code>';
                           $this->set_message('valid_static', "The %s : is not valid metadata.".$err_details);
                           return FALSE;
                        }


                }
                else
                {
                    $result = TRUE;
                }
                if($result)
                {
                    $entities_no = $docxml->getElementsbytagname('EntitiesDescriptor');
                    $entity_no = $docxml->getElementsbytagname('EntityDescriptor');
                    if($entities_no->length > 0)
                    {
                          $this->set_message('valid_static', "The %s : is not valid metadata<br />EntitiesDescriptor element is not allowed for single entity");
                          return FALSE;

                    }
                    if($entity_no->length != 1)
                    {
                          $this->set_message('valid_static', "The %s : is not valid metadata<br />exact one element EntityDescriptor is allowed");
                          return FALSE;

                    }
                    $ent_id = $entity_no->item(0)->getAttribute('entityID');
                    log_message('debug','-----"'.$ent_id.'" ".'.$compared_entityid.'"');
                    if(!empty($compared_entityid) && ($compared_entityid != $ent_id))
                    {
                          $this->set_message('valid_static', "The %s : is not valid metadata<br />entitID from static must match entityID in form");
                          return FALSE;
                    }
                    log_message('debug','PPPPPPPPPPPP'.$entity_no->item(0)->getAttribute('entityID'));
                     
               
                }
         }
      //  $this->CI->load->library('metadata_validator');
      //  $result = $this->CI->metadata_validator->validateWithSchema($metadata);

        if ($result === FALSE)
        {
            if (!empty($is_used))
            {
                log_message('debug', 'valid_static: result:: invalid metadata');
                           $err_details = "<br />Make sure elements contains namespaces ex. md:EntityDescriptor.";
                           $err_details .='<br />Also inside EntitiyDescriptor element you must declare namespaces defitions<br/> <code>xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata"  xmlns:shibmd="urn:mace:shibboleth:metadata:1.0" xmlns:mdui="urn:oasis:names:tc:SAML:metadata:ui" xmlns:ds="http://www.w3.org/2000/09/xmldsig#"</code>';
                           $this->set_message('valid_static', "The %s : is not valid metadata.".$err_details);
            }
            else
            {
                log_message('debug', 'valid_static: result:: invalid metadata, but ignored');
                $result = TRUE;
            }
        }
        return $result;
    }

    function valid_static_old($is_used, $metadata)
    {

        $metadata = trim(base64_decode($metadata));


        log_message('debug', '---- validation static metadata ------');
        log_message('debug', 'is_used::' . $is_used);
        log_message('debug', 'metadata::' . $metadata);
        if (empty($metadata))
        {
            log_message('debug', 'metadata --- empty');
        }
        else
        {
            log_message('debug', 'metadata --- not empty:');
        }
        $result = false;
        if (empty($metadata) && !empty($is_used))
        {
            log_message('debug', 'valid_static: result:: invalid metadata');
            $this->set_message('valid_static', "The %s : is empty.");
            return $result;
        }
        $this->CI->load->library('metadata_validator');
        $result = $this->CI->metadata_validator->validateWithSchema($metadata);

        if ($result === FALSE)
        {
            if (!empty($is_used))
            {
                log_message('debug', 'valid_static: result:: invalid metadata');
                $this->set_message('valid_static_old', "The %s : is not valid metadata.");
            }
            else
            {
                log_message('debug', 'valid_static_old: result:: invalid metadata, but ignored');
                $result = TRUE;
            }
        }
        return $result;
    }

}
