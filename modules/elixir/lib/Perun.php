<?php

require '/etc/elixir-proxy/elixir.php';

class sspmod_elixir_Perun
{
	const RPC_URL = 'https://perun.elixir-czech.cz/krb/rpc/';
	private static $user = USERNAME;
	private static $pass = PASSWORD;


	public static function get($manager, $method, $params) {

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, self::RPC_URL .'json/'.  $manager .'/'. $method .'?'. http_build_query($params));
		curl_setopt($ch, CURLOPT_USERPWD, self::$user . ":" . self::$pass);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$json = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($json, true);
		
		if (isset($result['errorId'])) {
			throw new SimpleSAML_Error_Exception("elixir:Perun: Call ".self::RPC_URL .'json/'.  $manager .'/'. $method .'?'. http_build_query($params)." failed. \n" . $result['name'] . "\n" . $result['message']);
		}
		
		return $result;
	}

	public static function post($manager, $method, $params) {
		$paramsJson = json_encode($params);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, self::RPC_URL .'json/'. $manager .'/'. $method);
		curl_setopt($ch, CURLOPT_USERPWD, self::$user . ":" . self::$pass);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsJson);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
			array('Content-Type:application/json',
				'Content-Length: ' . strlen($paramsJson))
		);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$json = curl_exec($ch);
		curl_close($ch);

                $result = json_decode($json, true);

                if (isset($result['errorId'])) {
                        throw new SimpleSAML_Error_Exception("elixir:Perun: Call ".self::RPC_URL .'json/'. $manager .'/'. $method."  failed. \n" . $result['name'] . "\n" . $result['message']);
                }

                return $result;
	}
	
}


