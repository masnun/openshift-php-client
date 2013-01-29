<?php

class OpenShiftDomain{
  private $domainName= "";
  private $dispatcher;
  private $apps;

  public function __construct($domainName){
    $openShift = ObjectBroker::get("openshift");
    $this->dispatcher = $openShift->getDispatcher();
    $this->domainName= $domainName;
  }

  public function updateName($newName){
    if($newName=="")
      throw new Exception("Valid domain name is required to update the name", 1);
      
    $url = "https://openshift.redhat.com/broker/rest/domains/{$this->domainName}";
    $params = array("id"=>$newName);
    $data = $this->dispatcher->put($url,$params);
    return $data;
  }

  public function delete($domainName){
    $url = "https://openshift.redhat.com/broker/rest/domains/{$domainName}";
    throw new Exception("Sorry dude, considering the risk - we have disabled deleting domains from this Library.");
    // $data = $this->dispatcher->delete($url);
    // return $data;
  }

  public function getApps(){
    $url = "https://openshift.redhat.com/broker/rest/domains/{$this->domainName}/applications";
    $apps = $this->dispatcher->get($url);
    foreach ($apps->data as $app){
      $_app = new OpenShiftApp($app->name, $this->domainName, $app); //casting
      $this->apps[$app->name] = $_app; 
    }
    return $apps;
  }

  public function getApp($appName, $preFetchDetails = true){
    if(!$this->apps[$appName]){
      if($preFetchDetails)
      $this->apps[$appName] = new OpenShiftApp($appName, $this->domainName);
        else
      $this->apps[$appName] = new OpenShiftApp($appName, $this->domainName,false,false);
    }
    return $this->apps[$appName];
  }

  public function getDomainDetails(){
    $openshift = ObjectBroker::get("openshift");
    $dispatcher = $openshift->getDispatcher();
    $url = "https://openshift.redhat.com/broker/rest/domains/{$this->domainName}";
    $data = $dispatcher->get($url);
    return $data;
  }
}