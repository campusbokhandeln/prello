<?php

namespace App\Support;

use App\Actions\GetTrelloBoardsAction;
use App\Actions\GetTrelloCardsAction;
use App\Actions\GetTrelloListsAction;
use App\Entities\TrelloBoard;
use App\Entities\TrelloCard;
use App\Entities\TrelloEntity;
use App\Entities\TrelloList;
use App\Entities\TrelloNull;
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
    protected $selectionType = TrelloCard::class;

    protected array $menuStack = [];
    protected TrelloSelection $trelloSelection;
    protected ?CliMenu $cliMenu = null;
    protected $result = null;

    public function __construct()
    {
        $this->trelloSelection = new TrelloSelection;
    }

    public function make(string $title, array $items = []): self
    {
        $this->menu = (new Menu($title))
            ->disableDefaultItems()
            ->addItems($items);

        return $this;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function addMenuToStack($menuCallback)
    {
        $this->menuStack[] = $menuCallback;
    }

    public function current()
    {
        collect($this->menuStack)->last()();
    }

    public function back(CliMenu $cliMenu = null)
    {
        array_pop($this->menuStack);

        if(count($this->menuStack) <= 1) {
            $this->cliMenu->close();
            return;
        }

        collect($this->menuStack)->last()($cliMenu);
    }

    /**
     * @param TrelloEntity[] $children
     * @param TrelloEntity $parent
     * @return callable
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function addTrelloEntities(array $children, TrelloEntity $parent): callable
    {
        $cb = function () use ($children, $parent) {
            $childItems = Collection::wrap($children)
                ->map(function (TrelloEntity $child) {
                    return new SelectableItem(
                        $child->getName(),
                        $this->getTrelloEntityCallback($child),
                    );
                });

            if($parent->getName() == 'null') {
                $this->cliMenu->setItems([]);
            }

            $this->cliMenu->setTitle($parent->getTitle());

            $backItem = new SelectableItem(
                '..',
                fn() => $this->back($this->cliMenu),
            );
            $this->cliMenu->addItem($backItem);

            $this->cliMenu->setSelectedItem($backItem);
            $this->cliMenu->addItems($childItems->toArray());

            if($parent->getName() == 'null' && $this->cliMenu->isOpen()) {
                $this->cliMenu->redraw();
            }
        };

        if (!count($this->menuStack)) {
            if($parent->getName() != 'null') {
                $parentCB = $this->addTrelloEntities(app(GetTrelloBoardsAction::class)->execute(), new TrelloNull());
                $this->addMenuToStack($parentCB);
            }
            $this->addMenuToStack($cb);
        }

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
        $this->selectionType = TrelloList::class;
        return $this;
    }

    public function cardSelection()
    {
        $this->selectionType = TrelloCard::class;
        return $this;
    }

    public function boardSelection()
    {
        $this->selectionType = TrelloBoard::class;
        return $this;
    }

    public function getTrelloEntityCallback(TrelloEntity $trelloEntity)
    {
        if (get_class($trelloEntity) == $this->selectionType) {
            return function (CliMenu $cliMenu) use ($trelloEntity) {
                $trelloEntity->setTrelloSelection($this->trelloSelection);
                $this->setResult($this->trelloSelection);
                $cliMenu->close();
            };
        } else {
            return function (CliMenu $cliMenu) use ($trelloEntity) {

                $cb = function (CliMenu $cliMenu = null) use ($trelloEntity) {
                    $trelloEntity->setTrelloSelection($this->trelloSelection);

                    $cliMenu->setItems([]);

                    $children = $trelloEntity->getChildren();

                    $this->addTrelloEntities($children, $trelloEntity)($cliMenu);

                    $cliMenu->redraw();
                };

                $cb($cliMenu);

                $this->addMenuToStack($cb);
            };
        }
    }

    public function setTrelloEntity(TrelloEntity $trelloEntity): self
    {
        $trelloEntity->setTrelloSelection($this->trelloSelection);

        return $this;
    }
}
