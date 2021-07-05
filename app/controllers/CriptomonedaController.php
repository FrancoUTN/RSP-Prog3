<?php
require_once './models/Criptomoneda.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Criptomoneda as Criptomoneda;

class CriptomonedaController implements IApiUsable
{

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombre = $parametros['nombre'];
    
    
    $archivos = $request->getUploadedFiles();
    $destino = "./FotosCriptomonedas/";
    $nombreAnterior = $archivos['foto']->getClientFilename();
    $extension = explode(".", $nombreAnterior);
    $extension = array_reverse($extension);
    $pathFoto = $destino . $nombre . "." . $extension[0];
    $archivos['foto']->moveTo($pathFoto);


    $criptomoneda = new Criptomoneda();
    $criptomoneda->precio = $parametros['precio'];
    $criptomoneda->nombre = $nombre;
    $criptomoneda->foto = $pathFoto;
    $criptomoneda->nacionalidad = $parametros['nacionalidad'];
    $criptomoneda->save();


    $payload = json_encode(array("mensaje" => "Criptomoneda creada con exito"));

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por nombre
    $id = $args['id'];

    // Buscamos por primary key
    $criptomoneda = Criptomoneda::find($id);

    // Buscamos por attr usuario
    // $usuario = Usuario::where('usuario', $usr)->first();

    $payload = json_encode($criptomoneda);

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Criptomoneda::all();

    $payload = json_encode($lista);

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerNac($request, $response, $args)
  {
    $nacionalidad = $args["nacionalidad"];

    $lista = Criptomoneda::where('nacionalidad', '=', $nacionalidad)->get();

    $payload = json_encode($lista);

    $response->getBody()->write($payload);
    // $response->getBody()->write($nacionalidad);

    return $response->withHeader('Content-Type', 'application/json');
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

    // Buscamos
    $criptomoneda = Criptomoneda::find($id);

    // Borramos
    $criptomoneda->delete();

    $payload = json_encode(array("mensaje" => "Criptomoneda borrada con exito"));

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }
}
