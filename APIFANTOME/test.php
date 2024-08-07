<?php
// Routeur simple pour gérer les requêtes vers /apighostphp
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($requestUri === '/') {
    // Vérifie la méthode de la requête
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Récupère les en-têtes de la requête
        $headers = getallheaders();
        
        // Vérifie l'en-tête Authorization
        if (isset($headers['Authorization']) && $headers['Authorization'] === 'Bearer coucou') {
            // Réponse JSON pour une autorisation réussie
            $response = [
                'status' => 'success',
                'message' => 'Requête reçue avec succès'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // Réponse JSON pour une autorisation échouée
            http_response_code(401);
            $response = [
                'status' => 'error',
                'message' => 'Non autorisé'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    } else {
        // Réponse JSON pour une méthode non supportée
        http_response_code(405);
        $response = [
            'status' => 'error',
            'message' => 'Méthode non supportée'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
    }
} else {
    // Réponse pour une route non trouvée
    http_response_code(404);
    $response = [
        'status' => 'error',
        'message' => 'Route non trouvée'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>