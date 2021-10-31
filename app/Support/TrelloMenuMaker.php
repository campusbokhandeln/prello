<?php

namespace App\Support;

use App\Actions\GetTrelloBoardsAction;
use App\Actions\GetTrelloCardsAction;
use App\Actions\GetTrelloListsAction;
use App\Entities\TrelloBoard;
use App\Entities\TrelloCard;
use App\Entities\TrelloList;
use Illuminate\Support\Collection;
use NunoMaduro\LaravelConsoleMenu\Menu;
use PhpSchool\CliMenu\Action\ExitAction;
use PhpSchool\CliMenu\Action\GoBackAction;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\MenuItem\SelectableItem;

class TrelloMenuMaker
{
    protected ?Menu $menu;
    protected $selectionType = 'card';

    protected array $history = [];
    protected TrelloSelection $trelloSelection;
    protected ?CliMenu $cliMenu = null;
    protected $result = null;

    public function __construct()
    {
        $this->trelloSelection = new TrelloSelection;
    }

    public function make(string $title, array $items = [], callable $back = null): self
    {
        $back ??= new ExitAction;

        $this->menu = (new Menu($title))
            ->disableDefaultItems()
            ->addItems($items);

        return $this;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function createMenu($menuCallback)
    {
        $this->history[] = $menuCallback;
    }

    public function current()
    {
        collect($this->history)->last()();
    }

    public function back(CliMenu $cliMenu = null)
    {
        array_pop($this->history);
        collect($this->history)->last()($cliMenu);
    }

    /**
     * @param TrelloCard[] $cards
     * @return $this
     */
    public function addCards(array $cards): callable
    {
        $cb = function () use ($cards) {
            $cardItems = Collection::wrap($cards)
                ->map(function (TrelloCard $card) {
                    return new SelectableItem(
                        $card->name,
                        $this->getCardCallback($card, $this->menu),
                    );
                });

            $this->cliMenu->setTitle('Select Card');
            $this->trelloSelection->trelloList
                ? $this->cliMenu->setTitle(sprintf('%s -> Select Card', $this->trelloSelection->trelloList->name))
                : $this->cliMenu->setTitle('Select Card');

            $backItem = new SelectableItem(
                '..',
                fn() => $this->back($this->cliMenu),
            );
            $this->cliMenu->addItem($backItem);

            $this->cliMenu->setSelectedItem($backItem);
            $this->cliMenu->addItems($cardItems->toArray());
        };

        if(! count($this->history)) {
            $this->addBoards(app(GetTrelloBoardsAction::class)->execute());
            $this->createMenu($cb);
        }

        return $cb;
    }

    /**
     * @param TrelloList[] $lists
     * @return $this
     */
    public function addLists(array $lists): callable
    {
        $cb = function (CliMenu $cliMenu = null)  use ($lists) {
            $listItems = Collection::wrap($lists)
                ->map(function (TrelloList $list) use ($lists) {
                    return new SelectableItem(
                        $list->name,
                        $this->getListCallBack($list, $this->menu, $lists),
                    );
                });

            $this->trelloSelection->trelloBoard
                ? $this->cliMenu->setTitle(sprintf('%s -> Select List', $this->trelloSelection->trelloBoard->name))
                : $this->cliMenu->setTitle('Select List');

            $backItem = new SelectableItem(
                '..',
                fn() => $this->back($cliMenu),
            );
            $this->cliMenu->addItem($backItem);
            $this->cliMenu->setSelectedItem($backItem);

            $this->cliMenu->addItems($listItems->toArray());
        };

        if(! count($this->history)) {
            $this->addBoards(app(GetTrelloBoardsAction::class)->execute());
            $this->createMenu($cb);
        }

        return $cb;
    }


    /**
     * @param TrelloBoard[] $boards
     * @return $this
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function addBoards(array $boards): callable
    {

        $cb = function (CliMenu $cliMenu = null) use ($boards) {
            $this->cliMenu->setItems([]);
            $this->cliMenu->setTitle('Select Board');
            $boardItems = Collection::wrap($boards)
                ->map(function (TrelloBoard $board) use ($boards) {
                    return new SelectableItem(
                        $board->name,
                        $this->getBoardCallBack($board, $this->menu, $boards),
                    );
                });
            $this->cliMenu->addItem(new SelectableItem('..', new ExitAction));
            $this->cliMenu->addItems($boardItems->toArray());
            $this->cliMenu->isOpen() ? $this->cliMenu->redraw() : null;
        };

        $this->createMenu($cb);

        return $cb;
    }

    public function build()
    {
        $this->cliMenu = $this->menu->build();
        return $this;
    }

    public function open(): TrelloSelection
    {
        $this->current();
        $this->cliMenu->open();

        return $this->result;
    }

    public function listSelection()
    {
        $this->selectionType = 'list';
        return $this;
    }

    public function cardSelection()
    {
        $this->selectionType = 'card';
        return $this;
    }

    public function boardSelection()
    {
        $this->selectionType = 'board';
        return $this;
    }

    public function getListCallback(TrelloList $list, Menu $menu, $lists)
    {
        if ($this->selectionType == 'list') {
            return function (CliMenu $cliMenu) use ($list) {
                $this->trelloSelection->trelloList = $list;
                $this->setResult($this->trelloSelection);
                $cliMenu->close();
            };
        } else {
            return function (CliMenu $cliMenu) use ($list, $lists) {

                $cb = function ($cliMenu = null) use ($list, $lists) {
                    $this->trelloSelection->trelloList = $list;
                    collect($cliMenu->getItems())
                        ->each(fn($item) => $cliMenu->removeItem($item));


                    $cards = app(GetTrelloCardsAction::class)->execute($list->id);

                    $this->addCards($cards)($cliMenu);
                    $cliMenu?->redraw();
                };

                $cb($cliMenu);

                $this->createMenu($cb);
            };
        }

        // If we dont have list selection, callback should redraw menu with cards.

    }

    public function getBoardCallback(TrelloBoard $board, Menu $menu, $boards)
    {
        if ($this->selectionType == 'board') {
            return function (CliMenu $cliMenu) use ($board) {
                $this->trelloSelection->trelloBoard = $board;
                $this->setResult($this->trelloSelection);
                $cliMenu->close();
            };
        } else {
            return function (CliMenu $cliMenu) use ($board, $boards) {
                $cb = function (CliMenu $cliMenu) use ($board, $boards) {
                    $this->trelloSelection->trelloBoard = $board;
                    // Build List menu for board
                    collect($cliMenu->getItems())
                        ->each(fn($item) => $cliMenu->removeItem($item));
                    $this->addLists($board->lists)($cliMenu);
                    $cliMenu?->redraw();
                };

                $cb($cliMenu);

                $this->createMenu($cb);
            };
        }
    }

    public function getCardCallback(TrelloCard $card, Menu $menu)
    {
        return function (CliMenu $cliMenu) use ($card) {
            $this->trelloSelection->trelloCard = $card;
            $this->setResult($this->trelloSelection);
            $cliMenu->close();
        };
    }

    public function setTrelloBoard(TrelloBoard $trelloBoard): self
    {
        $this->trelloSelection->trelloBoard = $trelloBoard;

        return $this;
    }

    public function setTrelloList(TrelloList $trelloList): self
    {
        $this->trelloSelection->trelloList = $trelloList;

        return $this;
    }

    public function setTrelloCard(TrelloCard $trelloCard): self
    {
        $this->trelloSelection->trelloCard = $trelloCard;

        return $this;
    }
}
