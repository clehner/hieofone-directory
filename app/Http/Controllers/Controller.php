<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Config;
use DB;
use Google_Client;
use Mail;
use Session;
use Swift_Mailer;
use Swift_SmtpTransport;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	protected function activity_log($username, $action)
	{
		$data = [
			'username' => $username,
			'action' => $action,
			'created_at' => date('Y-m-d H:i:s')
		];
		DB::table('activity_log')->insert($data);
		return true;
	}

	protected function as_push_notification($as_uri, $action)
	{
		$as = DB::table('oauth_rp')->where('as_uri', '=', $request->input('as_uri'))->first();
		$params = [
			'client_id' => $as->client_id,
			'client_secret' => $as->client_id,
			'action' => $action
		];
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $as_uri);
        curl_setopt($ch,CURLOPT_FAILONERROR,1);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_TIMEOUT, 60);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,0);
        $domain_name = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        if ($httpCode !== 404 && $httpCode !== 0) {
            $endpoint = $as_uri . '/as_push_notification';
            $post_body = json_encode($params);
            $content_type = 'application/json';
            $ch1 = curl_init();
            curl_setopt($ch1,CURLOPT_URL, $endpoint);
            curl_setopt($ch1, CURLOPT_POST, 1);
            curl_setopt($ch1, CURLOPT_POSTFIELDS, $post_body);
            curl_setopt($ch1, CURLOPT_HTTPHEADER, [
                "Content-Type: {$content_type}",
                'Content-Length: ' . strlen($post_body)
            ]);
            curl_setopt($ch1, CURLOPT_HEADER, 0);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch1, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch1, CURLOPT_CONNECTTIMEOUT ,0);
            $output = curl_exec($ch1);
            curl_close ($ch1);
            $response['status'] = 'OK';
            $response['arr'] = json_decode($output, true);
        } else {
            $response['status'] = 'error';
			$response['message'] = 'Authorization Server is not able to be reached';
        }
        return $response;
	}

	protected function base64url_encode($data)
	{
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	protected function changeEnv($data = []){
		if(count($data) > 0){
			// Read .env-file
			$env = file_get_contents(base_path() . '/.env');
			// Split string on every " " and write into array
			$env = preg_split('/\s+/', $env);;
			// Loop through given data
			foreach((array)$data as $key => $value){
				$new = true;
				// Loop through .env-data
				foreach($env as $env_key => $env_value){
					// Turn the value into an array and stop after the first split
					// So it's not possible to split e.g. the App-Key by accident
					$entry = explode("=", $env_value, 2);
					// Check, if new key fits the actual .env-key
					if($entry[0] == $key){
						// If yes, overwrite it with the new one
						$env[$env_key] = $key . "=" . $value;
						$new = false;
					} else {
						// If not, keep the old one
						$env[$env_key] = $env_value;
					}
				}
				if ($new == true) {
					$env[$key] = $key . "=" . $value;
				}
			}
			// Turn the array back to an String
			$env = implode("\n", $env);
			// And overwrite the .env with the new data
			file_put_contents(base_path() . '/.env', $env);
			return true;
		} else {
			return false;
		}
	}

	protected function array_gender()
    {
        $gender = [
            'm' => 'Male',
            'f' => 'Female',
            'u' => 'Undifferentiated'
        ];
        return $gender;
    }

	protected function default_policy_type_old()
	{
		$return = [
			'login_direct',
			// 'login_md_nosh',
			'any_npi',
			// 'login_google',
			// 'login_uport',
			'public_publish_directory',
			'private_publish_directory',
			'last_activity'
		];
		return $return;
	}

	protected function default_policy_type($key=true)
	{
		$return = [
			'login_direct' => [
				'label' => 'Anyone signed-in directly to this Authorization Server sees and controls Everything',
				'description' => '<p>Any party that you invite directly to be an authorized user to this Authorization Server has access to your Protected Health Information (PHI).  For example, this can be a spouse, partner, family members, guardian, or attorney.</p>'
			],
			// 'login_md_nosh',
			'any_npi' => [
				'label' => 'Anyone that has a Doximity-verfied National Provider Identifier (NPI) sees these Resources',
				'description' => '<p>All individual HIPAA covered healthcare providers have a National Provider Identifier, including:</p>
					<ul>
						<li>Physicians</li>
						<li>Pharmacists</li>
						<li>Physician assistants</li>
						<li>Midwives</li>
						<li>Nurse practitioners</li>
						<li>Nurse anesthetists</li>
						<li>Dentsits</li>
						<li>Denturists</li>
						<li>Chiropractors</li>
						<li>Podiatrists</li>
						<li>Naturopathic physicians</li>
						<li>Clinical social workers</li>
						<li>Professional counselors</li>
						<li>Psychologists</li>
						<li>Physical therapists</li>
						<li>Occupational therapists</li>
						<li>Pharmacy technicians</li>
						<li>Atheletic trainers</li>
					</ul>
					<p>By setting this as a default, you allow any healthcare provider, known or unknown at any given time, to access and edit your protected health information.</p>'
			],
			// 'login_google',
			// 'login_uport',
			'public_publish_directory' => [
				'label' => 'Anyone can see and link to your Trustee in a Directory',
				'description' => '<p>Any party that has access to a Directory that you participate in can see where this resource is found.</p>'
			],
			// 'private_publish_directory' => [
			// 	'label' => 'Only previously authorized users can see where this resource is located in a Directory',
			// 	'description' => '<p>Only previously authorized users that has access to a Directory that you participate in can see where this resource is found.</p>'
			// ],
			'last_activity' => [
				'label' => 'Publish the most recent date and time when this Trustee was active to a Directory',
				'description' => '<p>Activity of a Trustee refers to when a client attempts to view/edit a resource, adding/updating/removing a resource server, or registering new clients.</p>'
			]
		];
		if ($key == true) {
			return array_keys($return);
		} else {
			return $return;
		}
	}

	protected function policy_build($set=false)
	{
		$arr = $this->default_policy_type(false);
		$return = '';
		if ($set == false) {
			$query = DB::table('owner')->first();
		} else {
			$query = DB::table('oauth_clients')->where('client_id', '=', Session::get('current_client_id'))->first();
		}
		foreach ($arr as $row_k => $row_v) {
			$return .= '<div class="form-group"><div class="col-sm-offset-2 col-sm-10"><div class="checkbox"><label>';
			$return .= '<input type="checkbox" name="consent_' . $row_k . '"';
			if ($set == false) {
				$val = $query->{$row_k};
			} else {
				$consent = 'consent_' . $row_k;
				$val = $query->{$consent};
			}
			if ($val == 1) {
				$return .= ' checked';
			}
			$return .= '>' . $row_v['label'] . '</label><button type="button" class="btn btn-info" data-toggle="collapse" data-target="#' . $row_k . '_detail" style="margin-left:20px">Details</button></div>';
			$return .= '<div id="' . $row_k . '_detail" class="collapse">' . $row_v['description'] . '</div>';
			$return .= '</div></div>';
		}
		return $return;
	}

	protected function fhir_display($result, $type, $data)
    {
        $title_array = $this->fhir_resources();
        $gender_arr = $this->array_gender();
        if ($type == 'Patient') {
			$data['content'] = $result['entry'][0]['resource']['text']['div'];
            // $data['content'] = '<div class="alert alert-success">';
            // $data['content'] .= '<strong>Name:</strong> ' . $result['entry'][0]['name'][0]['given'][0] . ' ' . $result['entry'][0]['name'][0]['family'][0];
            // $data['content'] .= '<br><strong>Date of Birth:</strong> ' . date('Y-m-d', strtotime($result['entry'][0]['birthDate']));
            // $data['content'] .= '<br><strong>Gender:</strong> ' . $gender_arr[strtolower(substr($result['entry'][0]['gender'],0,1))];
            // $data['content'] .= '</div>';
            // $data['content'] .= '<div class="list-group">';
            // foreach ($title_array as $title_k=>$title_v) {
            //     if ($title_k !== 'Patient') {
            //         $data['content'] .= '<a href="' . route('fhir_connect_display', [$title_k]) . '" class="list-group-item"><i class="fa ' . $title_v['icon'] . ' fa-fw"></i><span style="margin:10px;">' . $title_v['name'] . '</span></a>';
            //     }
            // }
            // $data['content'] .= '</div>';
        } else {
			$data['content'] = '<form role="form"><div class="form-group"><input class="form-control" id="searchinput" type="search" placeholder="Filter Results..." /></div>';
			$data['content'] .= '<ul class="list-group searchlist">';
			foreach ($result['entry'] as $entry) {
				$data['content'] .= '<li class="list-group-item">' . $entry['resource']['text']['div'] . '</li>';
			}
			$data['content'] .= '</ul>';
        }
        return $data;
    }

	protected function fhir_request($url, $response_header=false, $token='', $epic=false)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		if ($response_header == true) {
			curl_setopt($ch, CURLOPT_HEADER, 1);
		} else {
			curl_setopt($ch, CURLOPT_HEADER, 0);
		}
		if ($token != '') {
            if ($epic == true) {
                $content_type = 'application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Accept: {$content_type}",
                    'Authorization: Bearer ' . $token
                ]);
            } else {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Authorization: Bearer ' . $token
                ));
            }
        }
		$output = curl_exec($ch);
		// if ($response_header == true) {
			//$info = curl_getinfo($ch);
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($output, 0, $header_size);
			$headers = $this->get_headers_from_curl_response($header);
			if (empty($headers)) {
				$result = json_decode($output, true);
			} else {
				$header_val_arr = explode(', ', $headers[0]['WWW-Authenticate']);
				$header_val_arr1 = explode('=', $header_val_arr[1]);
				$body = substr($output, $header_size);
				$result = json_decode($body, true);
				$result['as_uri'] = trim(str_replace('"', '', $header_val_arr1[1]));
			}
			//$result['error'] = $output;
		// } else {
		//	$result = json_decode($output, true);
		// }
		curl_close($ch);
		return $result;
	}

	protected function fhir_resources()
    {
        $return = [
            'Condition' => [
                'icon' => 'fa-bars',
                'name' => 'Conditions',
                'table' => 'issues',
                'order' => 'issue'
            ],
            'MedicationStatement' => [
                'icon' => 'fa-eyedropper',
                'name' => 'Medications',
                'table' => 'rx_list',
                'order' => 'rxl_medication'
            ],
            'AllergyIntolerance' => [
                'icon' => 'fa-exclamation-triangle',
                'name' => 'Allergies',
                'table' => 'allergies',
                'order' => 'allergies_med'
            ],
            'Immunization' => [
                'icon' => 'fa-magic',
                'name' => 'Immunizations',
                'table' => 'immunizations',
                'order' => 'imm_immunization'
            ],
            'Patient' => [
                'icon' => 'fa-user',
                'name' => 'Patient Information',
                'table' => 'demographics',
                'order' => 'pid'
            ],
            'Encounter' => [
                'icon' => 'fa-stethoscope',
                'name' => 'Encounters',
                'table' => 'encounters',
                'order' => 'encounter_cc'
            ],
            'FamilyHistory' => [
                'icon' => 'fa-sitemap',
                'name' => 'Family History',
                'table' => 'other_history',
                'order' => 'oh_fh'
            ],
            'Binary' => [
                'icon' => 'fa-file-text',
                'name' => 'Documents',
                'table' => 'documents',
                'order' => 'documents_desc'
            ],
            'Observation' => [
                'icon' => 'fa-flask',
                'name' => 'Observations',
                'table' => 'tests',
                'order' => 'test_name'
            ]
        ];
        return $return;
    }

	protected function gen_secret()
	{
		$length = 512;
		$val = '';
		for ($i = 0; $i < $length; $i++) {
			$val .= rand(0, 9);
		}
		$fp = fopen('/dev/urandom', 'rb');
		$val = fread($fp, 32);
		fclose($fp);
		$val .= uniqid(mt_rand(), true);
		$hash = hash('sha512', $val, true);
		$result = rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
		return $result;
	}

	protected function gen_uuid()
	{
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			mt_rand(0, 0xffff),
			mt_rand(0, 0x0fff) | 0x4000,
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	protected function get_headers_from_curl_response($headerContent)
	{
		$headers = [];
		// Split the string on every "double" new line.
		$arrRequests = explode("\r\n\r\n", $headerContent);
		// Loop of response headers. The "count() -1" is to avoid an empty row for the extra line break before the body of the response.
		for ($index = 0; $index < count($arrRequests) -1; $index++) {
			foreach (explode("\r\n", $arrRequests[$index]) as $i => $line) {
				if ($i === 0) {
					$headers[$index]['http_code'] = $line;
				} else {
					list ($key, $value) = explode(': ', $line);
					$headers[$index][$key] = $value;
				}
			}
		}
		return $headers;
	}

	protected function group_policy($client_id, $types, $action)
	{
		// $types is an array of claims
		$default_policy_type = [
			'login_direct',
			'login_md_nosh',
			'any_npi',
			'login_google'
		];
		// Create default policy claims if they don't exist
		foreach ($default_policy_type as $default_claim) {
			$claims = DB::table('claim')->where('claim_value', '=', $default_claim)->first();
			if (!$claims) {
				$claims_data = [
					'name' => 'Group',
					'claim_value' => $default_claim
				];
				DB::table('claim')->insert($claims_data);
			}
		}
		// Find all existing default polices for the resource server
		$default_policies_old_array = [];
		$resource_set_id_array = [];
		$policies_array = [];
		$resource_sets = DB::table('resource_set')->where('client_id', '=', $client_id)->get();
		if ($resource_sets) {
			foreach ($resource_sets as $resource_set) {
				$resource_set_id_array[] = $resource_set->resource_set_id;
				$policies = DB::table('policy')->where('resource_set_id', '=', $resource_set->resource_set_id)->get();
				if ($policies) {
					foreach ($policies as $policy) {
						$policies_array[] = $policy->policy_id;
						$query1 = DB::table('claim_to_policy')->where('policy_id', '=', $policy->policy_id)->get();
						if ($query1) {
							foreach ($query1 as $row1) {
								$query2 = DB::table('claim')->where('claim_id', '=', $row1->claim_id)->first();
								if ($query2) {
									if (in_array($query2->claim_value, $default_policy_type)) {
										$default_policies_old_array[] = $policy->policy_id;
									}
								}
							}
						}
					}
				}
			}
		}
		// Remove all existing default policy scopes to refresh them, delete them all if action is to delete
		if (count($default_policies_old_array) > 0) {
			foreach ($default_policies_old_array as $old_policy_id) {
				DB::table('policy_scopes')->where('policy_id', '=', $old_policy_id)->delete();
				DB::table('claim_to_policy')->where('policy_id', '=', $old_policy_id)->delete();
				if ($action == 'delete') {
					DB::table('policy')->where('policy_id', '=', $old_policy_id)->delete();
				}
			}
		}
		if ($action !== 'delete') {
			// Identify resource sets without policies and create new policies
			// Get all resource set scopes to default policies
			if (count($resource_set_id_array) > 0) {
				foreach ($resource_set_id_array as $resource_set_id) {
					$query3 = DB::table('policy')->where('resource_set_id', '=', $resource_set_id)->first();
					if ($query3) {
						// Check if there is an existing policy with this resource set and attach all scopes these policies
						$policies1 = DB::table('policy')->where('resource_set_id', '=', $resource_set_id)->get();
						if ($policies1) {
							foreach ($policies1 as $policy1) {
								if (in_array($policy1->policy_id, $default_policies_old_array)) {
									foreach ($types as $type) {
										$query4 = DB::table('claim')->where('claim_value', '=', $type)->first();
										$data1 = [
										  'claim_id' => $query4->claim_id,
										  'policy_id' => $policy1->policy_id
										];
										DB::table('claim_to_policy')->insert($data1);
									}
									$resource_set_scopes = DB::table('resource_set_scopes')->where('resource_set_id', '=', $resource_set_id)->get();
									if ($resource_set_scopes) {
										foreach ($resource_set_scopes as $resource_set_scope) {
											$data2 = [
												'policy_id' => $policy1->policy_id,
												'scope' => $resource_set_scope->scope
											];
											DB::table('policy_scopes')->insert($data2);
										}
									}
								}
							}
						}
					} else {
						// Needs new policy
						$data3['resource_set_id'] = $resource_set_id;
						$policy_id = DB::table('policy')->insertGetId($data3);
						foreach ($types as $type1) {
							$query5 = DB::table('claim')->where('claim_value', '=', $type1)->first();
							$data4 = [
							  'claim_id' => $query5->claim_id,
							  'policy_id' => $policy_id
							];
							DB::table('claim_to_policy')->insert($data4);
						}
						$resource_set_scopes1 = DB::table('resource_set_scopes')->where('resource_set_id', '=', $resource_set_id)->get();
						if ($resource_set_scopes1) {
							foreach ($resource_set_scopes1 as $resource_set_scope1) {
								$data5 = [
									'policy_id' => $policy_id,
									'scope' => $resource_set_scope1->scope
								];
								DB::table('policy_scopes')->insert($data5);
							}
						}
					}
				}
			}
		}
	}

	protected function login_sessions($user, $client_id, $admin)
	{
		$owner_query = DB::table('owner')->first();
		$proxies = DB::table('owner')->where('sub', '!=', $owner_query->sub)->get();
		$proxy_arr = [];
		if ($proxies) {
			foreach ($proxies as $proxy_row) {
				$proxy_arr[] = $proxy_row->sub;
			}
		}
		$client = DB::table('oauth_clients')->where('client_id', '=', $client_id)->first();
		Session::put('client_id', $client_id);
		Session::put('owner', $owner_query->firstname . ' ' . $owner_query->lastname);
		Session::put('username', $user->username);
		Session::put('full_name', $user->first_name . ' ' . $user->last_name);
		Session::put('client_name', $client->client_name);
		Session::put('logo_uri', $client->logo_uri);
		Session::put('sub', $user->sub);
		Session::put('email', $user->email);
		Session::put('invite', 'no');
		Session::put('is_owner', 'no');
		if ($user->sub == $owner_query->sub || in_array($user->sub, $proxy_arr)) {
			if ($admin == 'yes') {
				Session::put('is_owner', 'yes');
			}
		}
		if ($owner_query->sub == $user->sub) {
			Session::put('invite', 'yes');
		}
		Session::save();
		return true;
	}

	protected function nexmo($number, $message)
	{
		$url = "https://rest.nexmo.com/sms/json";
		$post = http_build_query([
			'api_key' => env('NEXMO_API'),
			'api_secret' => env('NEXMO_SECRET'),
			'to' => '1' . $number,
			'from' => '15709315960',
			'text' => $message
		]);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}


	protected function add_user($code, $username='', $password='', $owner=false)
	{
		if (is_array($code)) {
			$first_name = $code['first_name'];
			$last_name = $code['last_name'];
			$email = $code['email'];
		} else {
			$query = DB::table('invitation')->where('code', '=', $code)->first();
			$first_name = $query->first_name;
			$last_name = $query->last_name;
			$email = $query->email;
		}
		$sub = $this->gen_uuid();
		if ($username == '') {
			$username = $this->gen_uuid();
			$password = sha1($username);
		} else {
			if ($password !== '') {
				$password = sha1($password);
			} else {
				$password = sha1($username);
			}
		}
		$data = [
			'username' => $username,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'password' => $password,
			'email' => $email,
			'sub' => $sub
		];
		DB::table('oauth_users')->insert($data);
		$data1 = [
			'email' => $email,
			'name' => $username
		];
		DB::table('users')->insert($data1);
		if ($owner == true) {
			$data1 = [
				'lastname' => $last_name,
				'firstname' => $first_name,
				'sub' => $sub
			];
			DB::table('owner')->insert($data1);
		}
		return true;
	}

	protected function update_user($user)
	{
		$query = DB::table('oauth_users')->where('username', '=', $user['username'])->first();
		if ($query) {
			$data = [
				'first_name' => $user['first_name'],
				'last_name' => $user['last_name'],
				'password' => sha1($user['password']),
				'email' => $user['email'],
			];
			DB::table('oauth_users')->where('username', '=', $query->username)->update($data);
			$data1 = [
				'email' => $user['email'],
			];
			DB::table('users')->where('name', '=', $query->username)->update($data1);
		}
		return true;
	}

	protected function npi_lookup($first, $last='')
	{
		$url = 'https://npiregistry.cms.hhs.gov/api/?version=2.1&';
		$fields_arr = [
			'number' => '',
			'first_name' => '',
			'last_name' => '',
			'enumeration_type' => '',
			'taxonomy_description' => '',
			'organization_name' => '',
			'address_purpose' => '',
			'city' => '',
			'state' => '',
			'postal_code' => '',
			'country_code' => '',
			'limit' => '',
			'skip' => ''
		];
		if ($last == '') {
			$fields_arr['number'] = $first;
		} else {
			$fields_arr['first_name'] = $first;
			$fields_arr['last_name'] = $last;
		}
		$fields = http_build_query($fields_arr);
		$url .= $fields;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_FAILONERROR,1);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_TIMEOUT, 15);
		$json = curl_exec($ch);
		curl_close($ch);
		$arr = json_decode($json, true);
		return $arr;
	}

	protected function send_mail($template, $data_message, $subject, $to)
	{
		// $google_client = DB::table('oauth_rp')->where('type', '=', 'google')->first();
		// $google = new Google_Client();
		// $google->setClientID($google_client->client_id);
		// $google->setClientSecret($google_client->client_secret);
		// $google->refreshToken($google_client->refresh_token);
		// $credentials = $google->getAccessToken();
		// $username = $google_client->smtp_username;
		// $password = $credentials['access_token'];
		// $config = [
		// 	'mail.driver' => 'smtp',
		// 	'mail.host' => 'smtp.gmail.com',
		// 	'mail.port' => 465,
		// 	'mail.from' => ['address' => null, 'name' => null],
		// 	'mail.encryption' => 'ssl',
		// 	'mail.username' => $username,
		// 	'mail.password' => $password,
		// 	'mail.sendmail' => '/usr/sbin/sendmail -bs'
		// ];
		// config($config);
		// extract(Config::get('mail'));
		// $transport = Swift_SmtpTransport::newInstance($host, $port, 'ssl');
		// $transport->setAuthMode('XOAUTH2');
		// if (isset($encryption)) {
		// 	$transport->setEncryption($encryption);
		// }
		// if (isset($username)) {
		// 	$transport->setUsername($username);
		// 	$transport->setPassword($password);
		// }
		// Mail::setSwiftMailer(new Swift_Mailer($transport));
		$owner = DB::table('owner')->first();
		Mail::send($template, $data_message, function ($message) use ($to, $subject, $owner) {
			$message->to($to)
				->from($owner->email, $owner->firstname . ' ' . $owner->lastname)
				->subject($subject);
		});
		return "E-mail sent.";
	}

	/**
	* SMS notifcation with TextBelt
	*
	* @return Response
	*/
	protected function textbelt($number, $message)
	{
		$url = "http://cloud.noshchartingsystem.com:9090/text";
		$message = http_build_query([
			'number' => $number,
			'message' => $message
		]);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}

	protected function zammad_request($url, $response_header=false, $token='', $data=[])
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		if ($response_header == true) {
			curl_setopt($ch, CURLOPT_HEADER, 1);
		} else {
			curl_setopt($ch, CURLOPT_HEADER, 0);
		}
		$header = [];
		if ($token != '') {
			$header[] = 'Authorization: Bearer ' . $token;
        }
		if (isset($data['title'])) {
			$post_body = json_encode($data);
            $content_type = 'application/json';
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
            $header[] = "Content-Type: {$content_type}";
            $header[] = 'Content-Length: ' . strlen($post_body);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$output = curl_exec($ch);
		// $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		// $header = substr($output, 0, $header_size);
		// $headers = $this->get_headers_from_curl_response($header);
		// if (empty($headers)) {
		// 	$result = json_decode($output, true);
		// } else {
		// 	$header_val_arr = explode(', ', $headers[0]['WWW-Authenticate']);
		// 	$header_val_arr1 = explode('=', $header_val_arr[1]);
		// 	$body = substr($output, $header_size);
		// 	$result = json_decode($body, true);
		// 	$result['as_uri'] = trim(str_replace('"', '', $header_val_arr1[1]));
		// }
		curl_close($ch);
		return $result;
	}

	/**
	* Helper library for CryptoJS AES encryption/decryption
	* Allow you to use AES encryption on client side and server side vice versa
	*
	* @author BrainFooLong (bfldev.com)
	* @link https://github.com/brainfoolong/cryptojs-aes-php
	*/
	/**
	* Decrypt data from a CryptoJS json encoding string
	*
	* @param mixed $passphrase
	* @param mixed $jsonString
	* @return mixed
	*/
	protected function cryptoJsAesDecrypt($passphrase, $jsonString)
	{
		$jsondata = json_decode($jsonString, true);
		try {
			$salt = hex2bin($jsondata["s"]);
			$iv  = hex2bin($jsondata["iv"]);
		} catch(Exception $e) { return null; }
		$ct = base64_decode($jsondata["ct"]);
		$concatedPassphrase = $passphrase.$salt;
		$md5 = array();
		$md5[0] = md5($concatedPassphrase, true);
		$result = $md5[0];
		for ($i = 1; $i < 3; $i++) {
			$md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
			$result .= $md5[$i];
		}
		$key = substr($result, 0, 32);
		$data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
		return json_decode($data, true);
	}
	/**
	* Encrypt value to a cryptojs compatiable json encoding string
	*
	* @param mixed $passphrase
	* @param mixed $value
	* @return string
	*/
	protected function cryptoJsAesEncrypt($passphrase, $value)
	{
		$salt = openssl_random_pseudo_bytes(8);
		$salted = '';
		$dx = '';
		while (strlen($salted) < 48) {
			$dx = md5($dx.$passphrase.$salt, true);
			$salted .= $dx;
		}
		$key = substr($salted, 0, 32);
		$iv  = substr($salted, 32,16);
		$encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
		$data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
		return json_encode($data);
	}
}
