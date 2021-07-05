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
    $id = $args['id'];

    $criptomoneda = Criptomoneda::find($id);

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

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id = $args['id'];

    $criptomoneda = Criptomoneda::find($id);

    if ($criptomoneda !== null)
    {
      $archivos = $request->getUploadedFiles();

      if ($archivos != null)
      {
        if ($criptomoneda->foto != null)
        {
          $oldname = $criptomoneda->foto;
    
          $explotado = explode("/", $oldname);
    
          $revertido = array_reverse($explotado);
    
          $newname = "./BACKUPVENTAS/" . $revertido[0];
    
          rename($oldname, $newname);
        }

        $destino = "./FotosCriptomonedas/";
        $nombreAnterior = $archivos['foto']->getClientFilename();
        $extension = explode(".", $nombreAnterior);
        $extension = array_reverse($extension);
        $pathFoto = $destino . $parametros['nombre'] . "." . $extension[0];
        $archivos['foto']->moveTo($pathFoto);
        
        $criptomoneda->foto = $pathFoto;
      }

      if (isset($parametros['precio']))
        $criptomoneda->precio = $parametros['precio'];

      if (isset($parametros['nombre']))
        $criptomoneda->nombre = $parametros['nombre'];

      if (isset($parametros['nacionalidad']))
        $criptomoneda->nacionalidad = $parametros['nacionalidad'];

      $criptomoneda->save();

      $payload = json_encode(array("mensaje" => "Criptomoneda modificada con exito!"));
    }
    else
    {
      $payload = json_encode(array("mensaje" => "Criptomoneda no encontrada."));
    }

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $id = $args['id'];

    $criptomoneda = Criptomoneda::find($id);

    $criptomoneda->delete();

    $payload = json_encode(array("mensaje" => "Criptomoneda borrada con exito"));

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }
}
