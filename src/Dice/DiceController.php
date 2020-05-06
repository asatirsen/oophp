<?php

namespace Asatir\Dice;

use Anax\Commons\AppInjectableInterface;
use Anax\Commons\AppInjectableTrait;

// use Anax\Route\Exception\ForbiddenException;
// use Anax\Route\Exception\NotFoundException;
// use Anax\Route\Exception\InternalErrorException;

/**
 * A sample controller to show how a controller class can be implemented.
 * The controller will be injected with $app if implementing the interface
 * AppInjectableInterface, like this sample class does.
 * The controller is mounted on a particular route and can then handle all
 * requests for that mount point.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DiceController implements AppInjectableInterface
{
    use AppInjectableTrait;

    /**
     * Initialize game and redirect to play the game.
     */
    public function initAction(): object
    {
        $response = $this->app->response;
        $session = $this->app->session;
        $game = new Game();
        $session->set("game", $game);
        return $response->redirect("dicev2/play");
    }


    /**
     * Play the game - show game status
     */
    public function playActionGet(): object
    {
        $session = $this->app->session;
        $page = $this->app->page;
        $game = $session->get("game");

        $title = "Play the game(v2)";

        $histogram = new Histogram();
        foreach ($game->computerPlayer()->diceHand()->dices() as $dice) {
            $histogram->injectData($dice);
        }
        foreach ($game->humanPlayer()->diceHand()->dices() as $dice) {
            $histogram->injectData($dice);
        }

        $data = [
            "game" => $game,
            "histogram" => $histogram
        ];
        $page->add("dicev2/dice_view", $data);
        //$app->page->add("dice/debug");

        return $page->render([
            "title" => $title,
        ]);
    }

    /**
     * Make a roll, save the turn or reset the game
     */
    public function playActionPost(): object
    {
        $request = $this->app->request;
        $response = $this->app->response;
        $session = $this->app->session;

        $game = $session->get("game");
        $humanroll = $request->getPost("humanroll");
        $computerroll = $request->getPost("computerroll");
        $save = $request->getPost("save");
        $reset = $request->getPost("reset");
        if ($humanroll) {
            $game->humanRoll();
        }
        if ($computerroll) {
            $game->computerRoll();
        }
        if ($save) {
            $game->humanSave();
        }
        if ($reset) {
            $game = new Game();
        }
        $session->set("game", $game);
        return $response->redirect("dicev2/play");
    }


    /**
     * This is the index method action, it handles:
     * ANY METHOD mountpoint
     * ANY METHOD mountpoint/
     * ANY METHOD mountpoint/index
     *
     * @return string
     */
    public function indexAction(): string
    {
        // Deal with the action and return a response.
        return "Start Dice";
    }

//    public function debugAction(): string
//    {
//        // Deal with the action and return a response.
//        return "debug dice game";
//    }
//
////
//    /**
//     * This sample method dumps the content of $app.
//     * GET mountpoint/dump-app
//     *
//     * @return string
//     */
//    public function dumpAppActionGet(): string
//    {
//        // Deal with the action and return a response.
//        $services = implode(", ", $this->app->getServices());
//        return __METHOD__ . "<p>\$app contains: $services";
//    }
//

//    /**
//     * Add the request method to the method name to limit what request methods
//     * the handler supports.
//     * GET mountpoint/info
//     *
//     * @return string
//     */
//    public function infoActionGet(): string
//    {
//        // Deal with the action and return a response.
//        return __METHOD__ . ", \$db is {$this->db}";
//    }
//
//
//    /**
//     * This sample method action it the handler for route:
//     * GET mountpoint/create
//     *
//     * @return string
//     */
//    public function createActionGet(): string
//    {
//        // Deal with the action and return a response.
//        return __METHOD__ . ", \$db is {$this->db}";
//    }
//
//
//    /**
//     * This sample method action it the handler for route:
//     * POST mountpoint/create
//     *
//     * @return string
//     */
//    public function createActionPost(): string
//    {
//        // Deal with the action and return a response.
//        return __METHOD__ . ", \$db is {$this->db}";
//    }
//
//
//    /**
//     * This sample method action takes one argument:
//     * GET mountpoint/argument/<value>
//     *
//     * @param mixed $value
//     *
//     * @return string
//     */
//    public function argumentActionGet($value): string
//    {
//        // Deal with the action and return a response.
//        return __METHOD__ . ", \$db is {$this->db}, got argument '$value'";
//    }
//
//
//    /**
//     * This sample method action takes zero or one argument and you can use - as a separator which will then be removed:
//     * GET mountpoint/defaultargument/
//     * GET mountpoint/defaultargument/<value>
//     * GET mountpoint/default-argument/
//     * GET mountpoint/default-argument/<value>
//     *
//     * @param mixed $value with a default string.
//     *
//     * @return string
//     */
//    public function defaultArgumentActionGet($value = "default"): string
//    {
//        // Deal with the action and return a response.
//        return __METHOD__ . ", \$db is {$this->db}, got argument '$value'";
//    }
//
//
//    /**
//     * This sample method action takes two typed arguments:
//     * GET mountpoint/typed-argument/<string>/<int>
//     *
//     * NOTE. Its recommended to not use int as type since it will still
//     * accept numbers such as 2hundred givving a PHP NOTICE. So, its better to
//     * deal with type check within the action method and throuw exceptions
//     * when the expected type is not met.
//     *
//     * @param mixed $value with a default string.
//     *
//     * @return string
//     */
//    public function typedArgumentActionGet(string $str, int $int): string
//    {
//        // Deal with the action and return a response.
//        return __METHOD__ . ", \$db is {$this->db}, got string argument '$str' and int argument '$int'.";
//    }
//
//
//    /**
//     * This sample method action takes a variadic list of arguments:
//     * GET mountpoint/variadic/
//     * GET mountpoint/variadic/<value>
//     * GET mountpoint/variadic/<value>/<value>
//     * GET mountpoint/variadic/<value>/<value>/<value>
//     * etc.
//     *
//     * @param array $value as a variadic parameter.
//     *
//     * @return string
//     */
//    public function variadicActionGet(...$value): string
//    {
//        // Deal with the action and return a response.
//        return __METHOD__ . ", \$db is {$this->db}, got '" . count($value) . "' arguments: " . implode(", ", $value);
//    }
//

    /**
     * Adding an optional catchAll() method will catch all actions sent to the
     * router. You can then reply with an actual response or return void to
     * allow for the router to move on to next handler.
     * A catchAll() handles the following, if a specific action method is not
     * created:
     * ANY METHOD mountpoint/**
     *
     * @param array $args as a variadic parameter.
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function catchAll(...$args)
    {
        // Deal with the request and send an actual response, or not.
        //return __METHOD__ . ", \$db is {$this->db}, got '" . count($args) . "' arguments: " . implode(", ", $args);
        return;
    }
}