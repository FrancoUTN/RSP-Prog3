<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Usuario as Usuario;

class UsuarioController implements IApiUsable
{

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    // Creamos el usuario
    $usr = new Usuario();
    $usr->nombre = $parametros['nombre'];
    $usr->mail = $parametros['mail'];
    $usr->clave = $parametros['clave'];
    $usr->tipo = $parametros['tipo'];
    $usr->save();

    $payload = json_encode(array("mensaje" => "Usuario creado correctamente"));

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por nombre
    $usr = $args['usuario'];

    // Buscamos por primary key
    $usuario = Usuario::find($usr);

    // Buscamos por attr usuario
    // $usuario = Usuario::where('usuario', $usr)->first();

    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::all();

    // $payload = json_encode(array("listaUsuario" => $lista));
    $payload = json_encode($lista);

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usrModificado = $parametros['usuario'];
    $usuarioId = $args['id'];

    // Conseguimos el objeto
    $usr = Usuario::where('id', '=', $usuarioId)->first();

    // Si existe
    if ($usr !== null) {
      // Seteamos un nuevo usuario
      $usr->usuario = $usrModificado;
      // Guardamos en base de datos
      $usr->save();
      $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $usuarioId = $args['id'];
    // Buscamos el usuario
    $usuario = Usuario::find($usuarioId);
    // Borramos
    $usuario->delete();

    $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  public function VerificarUno($request, $response)
  {
    $parsedBody = $request->getParsedBody();
        
    if (isset($parsedBody["mail"]) && isset($parsedBody["tipo"]) && isset($parsedBody["clave"]))
    {
      $mail = $parsedBody["mail"];
      $tipo = $parsedBody["tipo"];
      $clave = $parsedBody["clave"];
      
      $lista = Usuario::all();    

      foreach ($lista as $usuario)
          if ($usuario->mail == $mail)
              if ($usuario->tipo == $tipo)
                  if ($usuario->clave == $clave)
                  {
                      $data = array("tipo" => $usuario->tipo);                    
                      $payload = json_encode($data);

                      $response->getBody()->write($payload);

                      return $response
                              ->withHeader('Content-Type', 'application/json')
                              ->withStatus(200);
                  }
    }

    $data = array("mensaje" => "ERROR. Mail, tipo, o clave incorrectos.");

    $payload = json_encode($data);

    $response->getBody()->write($payload);

    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(403);
  }
}
