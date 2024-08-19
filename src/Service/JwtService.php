<?php
namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $secret='b9a32020fe00c1c4809c7e2daa94f4972401b2a80a8b7aa30783d3283d0c9741';
    private static string $scopeMailFile = 'scopeMail.json'; // Fichier pour stocker le scope mail
    private string $encryptionKey ='449c0dd850c72bef79d349a4db38df3bd2aeb64adf57ec8c2a268306c26757e1';

    public function createToken(array $payload): string
    {
        // Ajoutez une date d'expiration au token (facultatif)
        $payload['exp'] = (new \DateTime('+1 hour'))->getTimestamp();

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function verifyToken(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid JWT token.');
        }
    }
    public function stockScopeMail(string $id, string $scope): void
    {
        $scopeMailTable = $this->readScopeMailFromFile();
        $scopeMailTable[$id] = $scope;
        $this->writeScopeMailToFile($scopeMailTable);
    }

    public function getScopeMail(string $id): string
    {
        $scopeMailTable = $this->readScopeMailFromFile();
        if (!isset($scopeMailTable[$id])) {
            throw new \InvalidArgumentException('Scope mail not found.');
        }
        return $scopeMailTable[$id];
    }

    public function deleteScopeMail(string $id): void
{
    // Lire et déchiffrer les données du fichier JSON
    $scopeMailTable = $this->readScopeMailFromFile();
    
    // Vérifier si l'ID existe dans les données
    if (!isset($scopeMailTable[$id])) {
        throw new \InvalidArgumentException('Scope mail not found.');
    }
    
    // Supprimer l'entrée correspondant à l'ID
    unset($scopeMailTable[$id]);
    
    // Chiffrer et écrire les données mises à jour dans le fichier JSON
    $this->writeScopeMailToFile($scopeMailTable);
}
    private function readScopeMailFromFile(): array
    {
        if (!file_exists(self::$scopeMailFile)) {
            return [];
        }
        $encryptedJson = file_get_contents(self::$scopeMailFile);
        $json = $this->decrypt($encryptedJson, $this->encryptionKey);
        return json_decode($json, true) ?? [];
    }
    private function writeScopeMailToFile(array $scopeMailTable): void
    {
        $json = json_encode($scopeMailTable);
        $encryptedJson = $this->encrypt($json, $this->encryptionKey);
        file_put_contents(self::$scopeMailFile, $encryptedJson);
    }
    private function encrypt(string $data, string $key): string
    {
        $iv = random_bytes(16); // Générer un vecteur d'initialisation
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', base64_decode($key), 0, $iv);
        return base64_encode($iv . $encrypted); // Concaténer IV et données chiffrées
    }
    private function decrypt(string $data, string $key): string
    {
        $data = base64_decode($data);
        $iv = substr($data, 0, 16); // Extraire le vecteur d'initialisation
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', base64_decode($key), 0, $iv);
    }
    

}
