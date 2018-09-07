<?php

class Cf7pd_Curl
{
	public static function get_token()
	{
		$options = get_option('pipedrive_token');
		$token = $options['pipedrive_field_0'];
		return $token;
	}

	public static function validate_token()
	{
		$token = Cf7pd_Curl::get_token();
		$url = "https://api.pipedrive.com/v1/currencies?api_token=".$token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		
		if( !ini_get('safe_mode') )
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);		
		}
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		curl_close($ch);
		
		$result = json_decode($result, true);
		
		if(count($result) > 0)
		{
			if($result['success'] == true)
			{
				return true;
			}
			else
			{
				return false;
			}			
		}
		else
		{
			return false;
		}
	}

	public static function get_PersonFields()
	{
		$token = Cf7pd_Curl::get_token();
		$url = "https://api.pipedrive.com/v1/personFields?api_token=".$token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		
		if( !ini_get('safe_mode') )
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);		
		}
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, true);
	}
	
	public static function get_DealFields()
	{
		$token = Cf7pd_Curl::get_token();
		$url = "https://api.pipedrive.com/v1/dealFields?api_token=".$token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		
		if( !ini_get('safe_mode') )
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);		
		}
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, true);
	}
	
	public static function filter_fields($fields, $initial)
	{		
		$filter_fields = array();
		$excluded_fields = array('org_id', 'update_time', 'add_time', 'owner_id', 'visible_to', 'open_deals_count', 'next_activity_date', 'last_activity_date', 'id', 'lost_deals_count', 'closed_deals_count', 'activities_count', 'done_activities_count', 'undone_activities_count', 'email_messages_count', 'picture_id', 'last_incoming_mail_time', 'last_outgoing_mail_time', 'won_deals_count', 'creator_id', 'pipeline', 'status', 'stage_id', 'currency', 'close_time', 'person_id', 'user_id', 'creator_user_id', 'lost_reason', 'title', 'stage_change_time');		
		
		if(count($fields) > 0)
		{
			if(array_key_exists('data', $fields))
			{
				$fields = $fields['data'];
				for($x = 0; $x < count($fields); $x++)
				{
					if(in_array($fields[$x]['key'], $excluded_fields))
					{
						//do nothing
					}
					else
					{
						$item = array();
						$item['name'] = $fields[$x]['name'];			
						$item['id'] = $initial.'_'.$fields[$x]['key'];
						
						if(substr($fields[$x]['key'], -7) == 'country')
						{
							$item['type'] = 'select';
						}
						else if($fields[$x]['key'] == 'email')
						{
							$item['type'] = 'email';
						}
						else
						{
							if($fields[$x]['field_type'] == 'double')
							{
								$item['type'] = 'number';
							}
							else
							{
								$item['type'] = 'text';
							}
						}
						array_push($filter_fields, $item);	
					}
				}				
			}
		}
		
		return $filter_fields;
	}
	
	public static function key_PersonFields()
	{
		$fields = Cf7pd_Curl::get_PersonFields();
		$fields = $fields['data'];
		$filter = array();
		
		for($x = 0; $x < count($fields); $x++)
		{
			array_push($filter, $fields[$x]['key']);
		}
		
		return $filter;
	}
	
	public static function key_DealFields()
	{
		$fields = Cf7pd_Curl::get_DealFields();
		$fields = $fields['data'];
		$filter = array();
		
		for($x = 0; $x < count($fields); $x++)
		{
			array_push($filter, $fields[$x]['key']);
		}
		
		return $filter;
	}	
	
	public static function new_person($person_array)
	{
		$token = Cf7pd_Curl::get_token();
		$url = "https://api.pipedrive.com/v1/persons?api_token=".$token;
		$headers = array('Content-Type: application/json');
		
		$personFields = Cf7pd_Curl::key_PersonFields();
		$output = array();
		
		foreach($person_array as $key => $value)
		{
			$clean_key = preg_replace('/PIPEDRIVE\_PERSON\_/i', '', $key);
			
			if(preg_match('/PIPEDRIVE\_PERSON\_/i', $key))
			{
				if(in_array($clean_key, $personFields))
				{
					$output[$clean_key] = $value;
				}				
			}
		}	
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		
		if( !ini_get('safe_mode') )
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);		
		}
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($output));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);

		
		Cf7pd_Curl::new_deal($person_array, $result);
		//Cf7pd_Public::debug_log($result);
	}
	
	public static function new_deal($person_array, $result)
	{
		$token = Cf7pd_Curl::get_token();
		$url = "https://api.pipedrive.com/v1/deals?api_token=".$token;
		$headers = array('Content-Type: application/json');		
		$result = json_decode($result, true);
		$dealFields = Cf7pd_Curl::key_DealFields();
		$person_id = $result['data']['id'];
		
		$output = array();
		$output['person_id'] = $person_id;			

		foreach($person_array as $key => $value)
		{
			$clean_key = preg_replace('/PIPEDRIVE\_DEAL\_/i', '', $key);
			
			if(preg_match('/PIPEDRIVE\_DEAL\_/i', $key))
			{
				if(in_array($clean_key, $dealFields))
				{
					$output[$clean_key] = $value;
				}
			}
		}	
				
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		
		if( !ini_get('safe_mode') )
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);		
		}
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($output));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);	
		
		$result = json_decode($result, true);
		$deal_id = $result['data']['id'];

		Cf7pd_Curl::new_note($person_array, $person_id, $deal_id);
		//Cf7pd_Public::debug_log($result);	
	}
		
	
	public static function new_note($person_array, $person_id, $deal_id)
	{
		$token = Cf7pd_Curl::get_token();
		$url = "https://api.pipedrive.com/v1/notes?api_token=".$token;
		$headers = array('Content-Type: application/json');

		$filter['deal_id'] = $deal_id;
		$filter['person_id'] = $person_id;
		$filter['content'] = $person_array['notes'];
					
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		
		if( !ini_get('safe_mode') )
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);		
		}
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($filter));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);	

		//Cf7pd_Public::debug_log($result);
		
	}
	
	
}


?>