<?php
/**
 * Sistemas Especializados e Innovación Tecnológica, SA de CV
 * Image Optimizer
 *
 * v.1.0.0.0 - 2021-06-24
 */
namespace Mpsoft\ImageOptimizer;

use \GuzzleHttp\Client;
use \Exception;
use \Throwable;

class ImageOptimizer
{
    private $token = NULL;
    private $host = "image-optimizer.sitec-mx.com";

    private $guzzle = NULL;
    private $openapi = NULL;

    public const NO_IMPLEMENTADO = 0;
    public const ENDPOINT_ERROR_GENERAR = 950;
    public const RESPUESTA_NO_DISPONIBLE = 951;

    function __construct(string $token)
    {
        $this->token = $token;

        $this->guzzle = new Client();
    }

    private function ObtenerURLEndPoint(string $url, array $variables, ?array $querystrings)
    {
        $endpoint_elementos = array();
        $endpoint_elementos[] = $this->host;

        $url_elementos = explode("/", $url);
        foreach($url_elementos as $url_elemento) // Para cada elemento de la URL
        {
            if($url_elemento[0] == "<") // Si es una variable
            {
                $variable_nombre = substr($url_elemento, 1, -1);

                if( isset($variables[$variable_nombre]) ) // Si se proporciona la variable requerida
                {
                    $endpoint_elementos[] = $variables[$variable_nombre];
                }
                else // Si no se proporciona la variable requerida
                {
                    throw new Exception("No se proporcionó la variable '{$variable_nombre}'.");
                }
            }
            else // Si no es una variable
            {
                $endpoint_elementos[] = $url_elemento;
            }
        }

        $querystring = NULL;
        if($querystrings) // Si se proporciona query-string
        {
            $querystring = "?" . http_build_query($querystrings);
        }

        $endpoint_url = implode("/", $endpoint_elementos);
        return "https://{$endpoint_url}{$querystring}";
    }


    private function API_CALL(string $metodo, string $url, ?array $variables=NULL, ?array $querystrings=NULL, ?array $body=NULL)
    {
        $estado = array("estado"=>ImageOptimizer::NO_IMPLEMENTADO, "mensaje"=>"OK");

        if(!$variables) // Si no se proporcionan las variables
        {
            $variables = array();
        }

        // Calculamos la URL de la llamada
        $endpoint_url = NULL;
        try
        {
            $endpoint_url = $this->ObtenerURLEndPoint($url, $variables, $querystrings);
        }
        catch(Throwable $t) // Error al generar la URL de la llamada
        {
            $estado = array("estado"=>ImageOptimizer::ENDPOINT_ERROR_GENERAR, "mensaje"=>"Error al generar la URL de la llamada.", "debug"=> utf8_encode($t->getMessage()));
        }

        if($endpoint_url) // Si se obtiene la URL de la llamada
        {
            // Generamos las opciones
            $opciones = array();
            $opciones["auth"] = array("io", $this->token);

            if($body)
            {
                $opciones["json"] = $body;
            }

            $response = NULL;
            try
            {
                $response = $this->guzzle->request($metodo, $endpoint_url, $opciones);
            }
            catch(Throwable $t) // Error al generar la URL del Endpoint
            {
                $response = $t->getResponse();
            }

            if($response) // Si hay respuesta
            {
                $response_text = $response->getBody();
                $estado = json_decode($response_text, TRUE);
            }
            else // Si no hay respuesta
            {
                $estado = array("estado"=>ImageOptimizer::RESPUESTA_NO_DISPONIBLE, "mensaje"=>"Error al obtener la respuesta de la llamada.");
            }
        }

        return $estado;
    }

    private function ObtenerFirmaDeVariables(?array $variables = NULL)
    {
        if(!$variables)
        {
            $variables = array();
        }

        $variables_proporcionadas = array_keys($variables);
        asort($variables_proporcionadas);
        $variables_key = implode("-", $variables_proporcionadas);

        return $variables_key;
    }


	public function POST_Optimizar(?array $variables=NULL,?array $querystrings=NULL,?array $body=NULL){ $url = "optimizar"; return $this->API_CALL("POST", $url, $variables, $querystrings, $body); }
	public function POST_SesionLogin(?array $variables=NULL,?array $querystrings=NULL,?array $body=NULL){ $url = "sesion/login"; return $this->API_CALL("POST", $url, $variables, $querystrings, $body); }
	public function POST_SesionLogout(?array $variables=NULL,?array $querystrings=NULL,?array $body=NULL){ $url = "sesion/logout"; return $this->API_CALL("POST", $url, $variables, $querystrings, $body); }
	public function POST_SesionCuenta(?array $variables=NULL,?array $querystrings=NULL,?array $body=NULL){ $url = "sesion/cuenta"; return $this->API_CALL("POST", $url, $variables, $querystrings, $body); }
	public function POST_SesionConfirmar(?array $variables=NULL,?array $querystrings=NULL,?array $body=NULL){ $url = "sesion/confirmar"; return $this->API_CALL("POST", $url, $variables, $querystrings, $body); }
	public function POST_SesionSolicitarNuevaContrasena(?array $variables=NULL,?array $querystrings=NULL,?array $body=NULL){ $url = "sesion/solicitar-nueva-contrasena"; return $this->API_CALL("POST", $url, $variables, $querystrings, $body); }
	public function POST_SesionCambiarContrasena(?array $variables=NULL,?array $querystrings=NULL,?array $body=NULL){ $url = "sesion/cambiar-contrasena"; return $this->API_CALL("POST", $url, $variables, $querystrings, $body); }
	public function GET_Servicios(?array $variables=NULL,?array $querystrings=NULL){ $url = NULL; $variables_key = $this->ObtenerFirmaDeVariables($variables); switch($variables_key) { case "": $url = "servicios"; break; case "id": $url = "servicios/<id>"; break;  default: $url = "servicios/<id>"; break; } return $this->API_CALL("GET", $url, $variables, $querystrings, NULL); }
	public function POST_Servicios(?array $variables=NULL,?array $querystrings=NULL,?array $body=NULL){ $url = "servicios"; return $this->API_CALL("POST", $url, $variables, $querystrings, $body); }
	public function PATCH_Servicios(?array $variables=NULL,?array $querystrings=NULL,?array $body=NULL){ $url = "servicios/<id>"; return $this->API_CALL("PATCH", $url, $variables, $querystrings, $body); }
	public function DELETE_Servicios(?array $variables=NULL,?array $querystrings=NULL){ $url = "servicios/<id>"; return $this->API_CALL("DELETE", $url, $variables, $querystrings, NULL); }
	public function GET_Cuentas(?array $variables=NULL,?array $querystrings=NULL){ $url = NULL; $variables_key = $this->ObtenerFirmaDeVariables($variables); switch($variables_key) { case "": $url = "cuentas"; break; case "id": $url = "cuentas/<id>"; break;  default: $url = "cuentas/<id>"; break; } return $this->API_CALL("GET", $url, $variables, $querystrings, NULL); }
	public function POST_Cuentas(?array $variables=NULL,?array $querystrings=NULL,?array $body=NULL){ $url = "cuentas"; return $this->API_CALL("POST", $url, $variables, $querystrings, $body); }
	public function PATCH_Cuentas(?array $variables=NULL,?array $querystrings=NULL,?array $body=NULL){ $url = "cuentas/<id>"; return $this->API_CALL("PATCH", $url, $variables, $querystrings, $body); }
	public function DELETE_Cuentas(?array $variables=NULL,?array $querystrings=NULL){ $url = "cuentas/<id>"; return $this->API_CALL("DELETE", $url, $variables, $querystrings, NULL); }
	public function GET_Suscripciones(?array $variables=NULL,?array $querystrings=NULL){ $url = NULL; $variables_key = $this->ObtenerFirmaDeVariables($variables); switch($variables_key) { case "": $url = "suscripciones"; break; case "id": $url = "suscripciones/<id>"; break;  default: $url = "suscripciones/<id>"; break; } return $this->API_CALL("GET", $url, $variables, $querystrings, NULL); }
	public function POST_Suscripciones(?array $variables=NULL,?array $querystrings=NULL,?array $body=NULL){ $url = "suscripciones"; return $this->API_CALL("POST", $url, $variables, $querystrings, $body); }
	public function PATCH_Suscripciones(?array $variables=NULL,?array $querystrings=NULL,?array $body=NULL){ $url = "suscripciones/<id>"; return $this->API_CALL("PATCH", $url, $variables, $querystrings, $body); }
	public function DELETE_Suscripciones(?array $variables=NULL,?array $querystrings=NULL){ $url = "suscripciones/<id>"; return $this->API_CALL("DELETE", $url, $variables, $querystrings, NULL); }

}
