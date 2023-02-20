<?php
header('Content-Type: application/json; charset=utf-8');

$cpf = $_GET['cpf'];
$cpf = trim($cpf);
$cpf = str_replace(".", "", $cpf);
$cpf = str_replace("-", "", $cpf);
$cpf = str_replace(" ", "", $cpf);
$cpf = str_replace("-", "", $cpf);

$fusohorario = json_decode(file_get_contents("http://worldtimeapi.org/api/timezone/America/Sao_Paulo"));
$unixtime = $fusohorario->unixtime;
$datetime = $fusohorario->datetime;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://odin.sportingtech.com/api/generic/register/getCustomerByCpfNumber?cpfNumber=$cpf");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_ENCODING, "gzip");
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.125 Safari/537.36');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Host: odin.sportingtech.com',
'Connection: keep-alive',
'sec-ch-ua: "Chromium";v="110", "Not A(Brand";v="24", "Google Chrome";v="110"',
'Accept: application/json, text/plain, */*',
'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.125 Safari/537.36',
'Origin: https://m.esportesdasorte.com',
'Referer: https://m.esportesdasorte.com/',
'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7,es;q=0.6'
));
curl_setopt($ch, CURLOPT_POST, false);
$resposta = curl_exec($ch);

$strings = json_decode($resposta);

$firstName = $strings->data->firstName;
$secondName = $strings->data->secondName;
$surname = $strings->data->surname;
$birthdate = $strings->data->birthdate;

if (strpos($resposta, 'true')) {
  header("HTTP/1.1 200 OK");
  $json = [
    "statuscode" => 200,
    "resposta" => "sucesso!",
    "mensagem" => "CPF VÁLIDO!",
    "cpf" => "$cpf",
    "nomeCompleto" => "$firstName $secondName $surname",
    "nascimento" => "$birthdate",
    "datetime" => "$datetime",
    "timestamp" => "$unixtime",
    
  ];
  
} else {
  header("Status: 404 Not Found");
  $json = [
    "statuscode" => 404,
    "resposta" => "Mal-sucedido!",
    "cpf" => "$cpf",
    "mensagem" => "CPF NÃO É VÁLIDO OU NÃO ENCONTRADO NA BASE!",
    "datetime" => "$datetime",
    "timestamp" => "$unixtime",
    
  ];
}
print_r(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
