<?php
declare(strict_types=1);

class Command
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function list(): void
    {
        $contacts = (new ContactManager($this->pdo))->findAll();

        foreach ($contacts as $contact) {
            echo $contact . PHP_EOL;
        }
    }

    public function detail(int $id): void
    {
        $contact = (new ContactManager($this->pdo))
            ->findById($id);

        echo $contact . PHP_EOL;
    }

    public function create(
        string $name,
        string $email,
        string $phoneNumber
        ): void {

        (new ContactManager($this->pdo))
        ->create($name, $email, $phoneNumber);

        echo "Contact créé" . PHP_EOL;
    }

    public function delete(int $id): void
    {

        (new ContactManager($this->pdo))
        ->delete($id);

        echo "Contact supprimé" . PHP_EOL;
    }

    public function modify(
        int $id,
        string $name,
        string $email,
        string $phoneNumber
        ): void {

    (new ContactManager($this->pdo))
        ->modify(
            $id,
            $name,
            $email,
            $phoneNumber
        );
}


public function help(): void
{
    echo "Commandes disponibles :" . PHP_EOL;
    echo "list" . PHP_EOL;
    echo "    Affiche tous les contacts." . PHP_EOL . PHP_EOL;

    echo "detail id" . PHP_EOL;
    echo "    Affiche le détail d'un contact." . PHP_EOL . PHP_EOL;

    echo "create nom,email,telephone" . PHP_EOL;
    echo "    Crée un nouveau contact." . PHP_EOL . PHP_EOL;

    echo "delete id" . PHP_EOL;
    echo "    Supprime un contact." . PHP_EOL . PHP_EOL;

    echo "modify id" . PHP_EOL;
    echo "    Modifie un contact." . PHP_EOL . PHP_EOL;

    echo "help" . PHP_EOL;
    echo "    Affiche l'aide." . PHP_EOL . PHP_EOL;

    echo "quit" . PHP_EOL;
    echo "    Quitte l'application." . PHP_EOL;
}
}