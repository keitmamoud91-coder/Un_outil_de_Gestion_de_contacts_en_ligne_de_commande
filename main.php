<?php
declare(strict_types=1);
require_once 'Command.php';

// Connexion à la base 
class DBConnect{
    private PDO $pdo;

    public function __construct()
    {   
        try {
            $this->pdo = new PDO(
                "mysql:host=localhost;dbname=adresses",
                "root",
                "130915#FR?260325@FR=151125#sidi_MQL"
            );
            $this->pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
            );
        } 
        catch (PDOException $e) {
           throw $e;
        }
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
    $db = new DBConnect();
    $pdo = $db->getPdo();
    var_dump($db);
    var_dump($pdo);

    
class ContactManager
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT id, name, email, phone_number FROM contacts"
        );

        $contacts = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $contact = new Contact();
            $contact->setId((int) $row['id']);
            $contact->setName($row['name']);
            $contact->setEmail($row['email']);
            $contact->setPhoneNumber($row['phone_number']);
            $contacts[] = $contact;
        }

        return $contacts;
    }

    public function findById(int $id): ?Contact
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, name, email, phone_number
             FROM contacts
             WHERE id = :id"
        );

        $stmt->execute([
            'id' => $id
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            echo "Contact introuvable.\n";
            return null;
        }

        $contact = new Contact();
        $contact->setId((int) $row['id']);
        $contact->setName($row['name']);
        $contact->setEmail($row['email']);
        $contact->setPhoneNumber($row['phone_number']);

        return $contact;
    }

    public function create(
        string $name,
        string $email,
        string $phoneNumber
        ): void
        {
        $stmt = $this->pdo->prepare(
        "INSERT INTO contacts
        (name, email, phone_number)
        VALUES
        (:name, :email, :phone_number)"
    );
        $stmt->execute([
        'name' => $name,
        'email' => $email,
        'phone_number' => $phoneNumber
    ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare(
        "DELETE FROM contacts
         WHERE id = :id"
    );

        $stmt->execute([
        'id' => $id
    ]);
    }

    public function modify(
    int $id,
    string $name,
    string $email,
    string $phoneNumber
    ): void {

        $stmt = $this->pdo->prepare(
        "UPDATE contacts
         SET
            name = :name,
            email = :email,
            phone_number = :phone_number
         WHERE id = :id"
    );

        $stmt->execute([
        'id' => $id,
        'name' => $name,
        'email' => $email,
        'phone_number' => $phoneNumber
    ]);
}
}


class Contact {
    private ?int $id = null;
    private ?string $name = null;
    private ?string $email = null;
    private ?string $phoneNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function __toString(): string
    {
        return sprintf(
            "%d | %s | %s | %s",
            $this->id ?? 0,
            $this->name ?? '',
            $this->email ?? '',
            $this->phoneNumber ?? ''
        );
    }
}

while (true) {
    $line = readline("Entrez votre commande : ");
    //var_dump($line);

    if ($line === 'quit') {
        echo "Au revoir !" . PHP_EOL;
        break;
    }
    
    elseif ($line === "list") {
        (new Command($pdo))->list();
    }

    elseif (preg_match('/^detail\s+(\d+)$/', $line, $matches)) {
        $id = (int) $matches[1];
        (new Command($pdo))->detail($id);
    }

    elseif (preg_match('/^create\s+(.+)$/', $line, $matches)) {
        $contacts = explode(';', $matches[1]);
    foreach ($contacts as $contactData) {
        [$name, $email, $phoneNumber] =
            array_map('trim', explode(',', $contactData));

        (new Command($pdo))
            ->create($name, $email, $phoneNumber);
    }

    echo count($contacts) . " contacts créés." . PHP_EOL;
    }

    elseif (
    preg_match(
        '/^delete\s+([\d,]+)$/',
        $line,
        $matches )) {

    $ids = explode(',', $matches[1]);

    foreach ($ids as $id) {
        (new Command($pdo))
            ->delete((int) trim($id));
    }

    echo count($ids) . " contacts supprimés." . PHP_EOL;
    }

    elseif (
    preg_match(
        '/^modify\s+(.+)$/',
        $line,
        $matches)) {

    $contacts = explode(';', $matches[1]);

    foreach ($contacts as $contactData) {

        [
            $id,
            $name,
            $email,
            $phoneNumber
        ] = array_map(
            'trim',
            explode(',', $contactData)
        );

        (new Command($pdo)) ->modify(
            (int) $id,
            $name,
            $email,
            $phoneNumber
        );
    }

    echo count($contacts)
        . " contacts modifiés."
        . PHP_EOL;
    }

    elseif ($line === "help") {
    (new Command($pdo))->help();
    }

    else {
    echo "Commande inconnue." . PHP_EOL;
    echo "Tapez 'help' pour afficher les commandes disponibles." . PHP_EOL;
    }
}