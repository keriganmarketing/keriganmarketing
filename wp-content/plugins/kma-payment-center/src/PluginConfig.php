<?php

namespace KMAPaymentCenter;

class PluginConfig
{
    public $pluginDir;
    public $pluginSlug;
    public $pluginName;
    protected $processorConfig;

    public function __construct()
    {
        $this->pluginSlug = plugin_basename(__FILE__);
        $this->pluginName = plugin_basename(__FILE__);
        $this->pluginDir = dirname(dirname(__FILE__));
        $this->processorConfig = [
            'Authorize' => [
                'LIVE' => [
                    'API Login ID'        => 'your_LIVE_login_id',
                    'API Transaction Key' => 'your_LIVE_transaction_key'
                ],
                'TEST' => [
                    'API Login ID'        => 'your_SANDBOX_login_id',
                    'API Transaction Key' => 'your_SANDBOX_transaction_key'
                ]
            ]
        ];
    }

    public function getVar($var)
    {
        return $this->{$var};
    }

    public function setVar($var, $val)
    {
        $this->{$var} = $val;
    }

    public function setTerminalState($paymentTerminals, $action )
    {
        $output = [];

        foreach($paymentTerminals as $terminal => $details){
            $terminal = strtoupper(str_replace([
                ',',
                ' ',
                '.',
                '\''
            ], '_', $terminal));

            $wpdbVars = (is_string(get_option('kmapc_details_'.$terminal))) ?
                unserialize(get_option('kmapc_details_'.$terminal)) :
                get_option('kmapc_details_'.$terminal);

            $postVars = [];
            foreach($details as $case => $fields){
                $caseArray = [];
                if($case=='TEST'||$case == 'LIVE'){
                    foreach($fields as $field => $value){
                        $postName = strtoupper(str_replace(array(',',' ','.','\''), '_', $field));
                        $inputId = strtolower(str_replace(array(' ','"','\'','.'), '_', $terminal."_".$case."_".$field));
                        if(isset($_POST['kmapc_submit_settings']) && $_POST['kmapc_submit_settings'] == 'yes' && isset($_POST["{$inputId}"])){
                            $postValue = stripslashes_deep($_POST["{$inputId}"]);
                        } elseif($action == "install" || $action == "uninstall") {
                            $postValue = $value;
                        } else {
                            $postValue = $wpdbVars[strtolower($case)][$postName];
                        }

                        $caseArray[$postName] = $postValue;
                        $output[$terminal][$case][$field] = $postValue;
                    }
                }
                if($case == "TIP"){
                    $newarr[$terminal][$case]=$fields;
                    continue;
                }
                $case = strtolower($case);
                $postVars[$case] = $caseArray;
            }

            if($action == "install" || (isset($_POST['kmapc_submit_settings']) && $_POST['kmapc_submit_settings'] == 'yes')){
                $postVars = serialize($postVars);
                update_option('kmapc_details_'.$terminal,$postVars);
            }elseif($action == "uninstall"){
                delete_option('kmapc_details_'.$terminal);
            }
        }
        unset($postVars);
        unset($caseArray);
        return $output;
    }

    public function displaySelectedCondition($i,$processor)
    {
        $condition = (($processor==$i && strlen($processor) > 0) ||
                      ($i==1 && strlen($processor) < 1 && !isset($_GET['active_terminal'])) ||
                      (isset($_GET['active_terminal']) && $i==$_GET['active_terminal'])
            ? ' selected' : '');
        return $condition;
    }

    public function isPluginFilter($fieldName)
    {
        if(strpos($fieldName, '[filter]') === false){
            return null;
        } else {
            return str_replace('[filter]','', $fieldName);
        }
    }

    public function displayErrors($array)
    {
        foreach($array as $terminal => $error){
            foreach($error as $key => $errorText){
                echo "ERROR :".$errorText."<br />";
            }
        }
    }

}