<?php


namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToDoController extends AbstractController
{
    #[Route('/toDo', name: 'app_to_do')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->has('listeTodos')) {
            $Todos = ['achat' => 'acheter clé usb', 'cours' => 'Finaliser mon cours',
                'correction' => 'corriger mes examens'];
            $session->set('listeTodos', $Todos);
        }
        return $this->render('to_do/listeToDo.html.twig');
    }

    #[Route('/addTodo/{name}/{action}', name: 'addtodo')]
    public function addTodo(Request $request, $name, $action)
    {
        $session = $request->getSession();
        if (!$session->has('listeTodos')) {
            $this->addFlash('errorInitialize', "Message flash:la liste des Todos n'est pas encore initialisée !");
        } else {
            $todolist = $session->get('listeTodos');
            if (isset($todolist[$name])) {
                $this->addFlash('successUpdate', "Message flash: Todo $name a été mis à jour avec succés ");
            } else {
                $this->addFlash('successAdd', "Message flash: Todo $name a été ajouté avec succès");
            }

            $todolist[$name] = $action;
            $session->set('listeTodos', $todolist);
        }
        return $this->forward('App\Controller\ToDoController::index', ['name' => $name, 'action' => $action]);
    }
    #[Route('/deleteTodo/{name}','todo.delete')]
    public function deleteTodo(Request $request,$name):Response{
        $session=$request->getSession();
        $todolist=$session->get('listeTodos');
        if (isset($todolist[$name])){
            unset($todolist[$name]);
            $this->addFlash('successDelete',"Message flash : Le Todo $name a été supprimé avec succès");
            $session->set('listeTodos',$todolist);
        }
        else {
            $this->addFlash('errorDelete',"Message flash: Le Todo $name que vous souhaitez supprimer n'existe pas dans la liste !");
        }
        return $this->forward('App\Controller\ToDoController::index',['name'=>$name]);
    }
    #[Route('/resetTodo','todo.reset')]
    public function resetTodo(Request $request):Response{
        $session=$request->getSession();
        if ($session->has('listeTodos')) {
            $session->remove("listeTodos");
            $this->addFlash('successReset', "Message flash : le reset des todos a été effectué avec succès");
        }
        else{
            $this->addFlash('errorInitialize', "Message flash : la liste des Todos n'est pas encore initialisée !");
        }
       return $this->forward("App\Controller\ToDoController::index");
    }

}