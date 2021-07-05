<?php
require_once './models/Usuario.php';
require_once './models/Criptomoneda.php';
require_once './models/Venta.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Usuario as Usuario;
use \App\Models\Criptomoneda as Criptomoneda;
use \App\Models\Venta as Venta;

class VentaController implements IApiUsable
{

  public function CargarUno($request, $response, $args)
  {
    // Parámetros
    $parametros = $request->getParsedBody();

    $cantidad = $parametros['cantidad'];
    $id_usuario = $parametros['id_usuario'];
    $id_criptomoneda = $parametros['id_criptomoneda'];
    
    $usuario = Usuario::find($id_usuario);
    $criptomoneda = Criptomoneda::find($id_criptomoneda);

    // Foto
    $archivos = $request->getUploadedFiles();

    $destino = "./FotosCripto/";

    $nombreAnterior = $archivos['foto']->getClientFilename();
    $extension = explode(".", $nombreAnterior);
    $extension = array_reverse($extension)[0];
    
    $fecha = date("Y-m-d");

    $pathFoto = $destino . $criptomoneda->nombre . "+" . $usuario->nombre . "+" . $fecha . "." . $extension;

    $archivos['foto']->moveTo($pathFoto);

    // Creación
    $venta = new Venta();

    $venta->fecha = $fecha;
    $venta->cantidad = $cantidad;
    $venta->id_usuario = $id_usuario;
    $venta->id_criptomoneda = $id_criptomoneda;
    $venta->foto = $pathFoto;

    $venta->save();

    // Respuesta
    $payload = json_encode(array("mensaje" => "Venta creada correctamente"));

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $id = $args['id'];

    $venta = Venta::find($id);

    $payload = json_encode($venta);

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Venta::all();

    $payload = json_encode($lista);

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerAlemanas($request, $response, $args)
  {
  }

  public function ModificarUno($request, $response, $args)
  {
    // $parametros = $request->getParsedBody();

    // $usrModificado = $parametros['usuario'];
    // $usuarioId = $args['id'];

    // // Conseguimos el objeto
    // $usr = Usuario::where('id', '=', $usuarioId)->first();

    // // Si existe
    // if ($usr !== null) {
    //   // Seteamos un nuevo usuario
    //   $usr->usuario = $usrModificado;
    //   // Guardamos en base de datos
    //   $usr->save();
    //   $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
    // } else {
    //   $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
    // }

    // $response->getBody()->write($payload);
    // return $response
    //   ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $id = $args['id'];

    $venta = Venta::find($id);

    $venta->delete();

    $payload = json_encode(array("mensaje" => "Venta borrada con exito"));

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }
}
