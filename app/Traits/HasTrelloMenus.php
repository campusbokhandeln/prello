<?php

namespace App\Traits;

use App\Actions\GetTrelloBoardAction;
use App\Actions\GetTrelloBoardsAction;
use App\Actions\GetTrelloCardsAction;
use App\Actions\GetTrelloListAction;
use App\Actions\GetTrelloListsAction;
use App\Entities\TrelloBoard;
use App\Entities\TrelloCard;
use App\Entities\TrelloList;
use App\Exceptions\InvalidSettingsException;
use App\Services\Settings\Settings;
use App\Services\Trello\TrelloApiGateway;
use App\Support\TrelloMenuMaker;
use App\Support\TrelloSelection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

trait HasTrelloMenus
{
    protected TrelloApiGateway $trello;

    public function getList($boardId = null): TrelloSelection
    {
        $board = null;
        try {
            $board = app(GetTrelloBoardAction::class)->execute($boardId);
        } catch (\Exception $e) {

        }
        try {

            $menu = app(TrelloMenuMaker::class);

            if ($board) {
                $lists = app(GetTrelloListsAction::class)->execute($board->id);

                $menu->make(sprintf('%s -> Choose List', $board->name))
                    ->listSelection()
                    ->build()
                    ->setTrelloBoard($board)
                    ->addLists($lists);
            } else {
                $boards = app(GetTrelloBoardsAction::class)->execute();

                $menu->make('Choose Board')
                    ->listSelection()
                    ->build()
                    ->addBoards($boards);
            }

            $trelloSelection = $menu->open();

            if (!$trelloSelection->trelloList) {
                throw new \Exception('No list chosen');
            }

            return $trelloSelection;
        } catch (\Throwable $e) {
            $this->error('List not OK');
            exit();
        }
    }

    public function getCard($boardId = null, $listId = null): TrelloSelection
    {
        $board = null;
        $list = null;
        try {
            $board = app(GetTrelloBoardAction::class)->execute($boardId);
            $list = app(GetTrelloListAction::class)->execute($listId);
        } catch (\Exception $e) {

        }

        try {
            $menu = app(TrelloMenuMaker::class);
            if ($board && $list) {

                $cards = app(GetTrelloCardsAction::class)->execute($list->id);

                $menu->make(sprintf('%s -> Choose Card', $list->name))
                    ->cardSelection()
                    ->build()
                    ->setTrelloBoard($board)
                    ->setTrelloList($list)
                    ->addCards($cards);
            } else if ($board) {
                $lists = app(GetTrelloListsAction::class)->execute($board->id);

                $menu->make(sprintf('%s -> Choose List', $board->name))
                    ->cardSelection()
                    ->build()
                    ->setTrelloBoard($board)
                    ->addLists($lists);
            } else {
                $boards = app(GetTrelloBoardsAction::class)->execute();

                $menu->make('Choose Board')
                    ->cardSelection()
                    ->build()
                    ->addBoards($boards);
            }

            $trelloSelection = $menu->open();

            if (!$trelloSelection->trelloCard) {
                throw new \Exception('No Card chosen');
            }

            return $trelloSelection;
        } catch (\Throwable $e) {
            $this->error('Card not OK');
            exit();
        }
    }


    public function saveTrelloSelectionFor(string $selectionType, TrelloSelection $trelloSelection)
    {
        $settings = app(Settings::class);

        if ($trelloSelection->trelloBoard) {
            $settings->set("{$selectionType}.lastBoardId", $trelloSelection->trelloBoard->id);
        }

        if ($trelloSelection->trelloList) {
            $settings->set("{$selectionType}.lastListId", $trelloSelection->trelloList->id);
        }

        $settings->save();
    }
}
