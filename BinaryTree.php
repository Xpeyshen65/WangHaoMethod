<?php

class BinaryNode
{
    public $value;
    public $left  = NULL;
    public $right = NULL;
    public $prev = NULL;

    public function __construct ($value)
    {
        $this->value = $value;
    }


}

class BinaryTree
{
    protected $root = NULL;

    public function isEmpty ()
    {
        return is_null($this->root);
    }

    public function insert ($value, $way)
    {
        $node = new BinaryNode($value);
        $this->insertNode($node, $this->root, $way);
    }

    protected function insertNode (BinaryNode $node, &$subtree, $way)
    {
        if (is_null($subtree))
        {
            $subtree = $node;
        }
        else
        {
            if ($way == 'left')
            {
                $root = $subtree;
                $this->insertNode($node, $subtree->left, '');
                $this->insertNode($this->root, $subtree->left->prev, '');
            }
            elseif ($way == 'right')
            {
                $this->insertNode($node, $subtree->right, '');
                $this->insertNode($subtree, $subtree->right->prev, '');
            }
            else {
                echo "Element don't insert";
            }
        }
        return $this;
    }

    /*public function find ($value) {
        $node = &$this->findNode($value, $this->root);
        if ($node) {
            return
        }
    }*/

    protected function &findNode ($value, &$subtree)
    {
        // Если элемент не найден, возвращаем FALSE
        if (is_null($subtree))
        {
            return FALSE;
        }

        // Для искомого значения меньшего, чем значение узла, продолжаем искать в левом поддереве
        if ($subtree->value > $value)
        {
            return $this->findNode($value, $subtree->left);
        }
        // Для искомого значения большего, чем значение узла, продолжаем искать в правом поддереве
        elseif ($subtree->value < $value)
        {
            return $this->findNode($value, $subtree->right);
        }
        // Если искомое значение равно значению узла, то возвращаем этот узел
        else
        {
            return $subtree;
        }
    }

    public function delete ($value)
    {
        // Выьрасываем исключение при попытке удаления элемента из пустого дерева
        if ($this->isEmpty())
        {
            throw new UnderflowException('Tree is empty!');
        }

        // Ищем нод в дереве
        $node = &$this->findNode($value, $this->root);
        // Если нод найден в дереве, то рекурсивно удаляем его
        if ($node)
        {
            $this->deleteNode($node);
        }
        return $this;
    }

// Метод рекурсивного удаления, найденого нода из дерева
    protected function deleteNode (BinaryNode &$node)
    {
        // Если у узла нет потомков, удаляем его
        if (is_null($node->left) && is_null($node->right))
        {
            $node = NULL;
        }
        // Если у узла нет левого поддерева, заменяем его правым поддеревом
        elseif (is_null($node->left))
        {
            $node = $node->right;
        }
        // Если у узла нет правого поддерева, заменяем его левым поддеревом
        elseif (is_null($node->right))
        {
            $node = $node->left;

        }
        // Если у узла есть оба потомка
        else
        {
            // Если у правого поддерева нет левого потомка, то заменяем удаляемый узел правым потомком, сохраняя левую ветвь удаляемого узла
            if (is_null($node->right->left))
            {
                $node->right->left = $node->left;
                $node = $node->right;
            }
            // если у правого поддерева есть левый потомок, то копируем его значение в удаляемый узел и рекурсивно удаляем
            else
            {
                $node->value = $node->right->left->value;
                $this->deleteNode($node->right->left);
            }
        }
    }

    public function getLeft() {
        return $this->root->left;
    }

    public function getRight() {
        if (is_null($this->root->right)) {
            return null;
        }
        else {
            return $this->root->right;
        }
    }

    public function getLeftValue() {
        return $this->root->left->value;
    }

    public function getRightValue() {
        return $this->root->right->value;
    }

    public function getRoot() {
        return $this->root;
    }

    public function getRootValue() {
        return $this->root->value;
    }

    public function getPrev() {
        return $this->root->prev;
    }

    public function getPrevValue() {
        return $this->root->prev->value;
    }

    public function setRoot($value) {
        $this->root = $value;
    }

}