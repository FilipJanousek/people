<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use App\Middleware\JsonBodyParser\Middleware as JsonBodyParserMiddleware;

require_once('config.php');
require_once('vendor/autoload.php');
require_once('src/middleware/jsonBodyParser/Middleware.php');

const CONTACT_TYPE_EMAIL = 'EMAIL';
const CONTACT_TYPE_PHONE = 'PHONE';

const CATEGORY_CODE_ACTOR = 'ACTOR';
const CATEGORY_CODE_MUSICIAN = 'MUSICIAN';
const CATEGORY_CODE_FAMILY = 'FAMILY';

$db = new Dibi\Connection($db_config);
$app = AppFactory::create();

$app->add(new JsonBodyParserMiddleware());

$app->group('/api', function (RouteCollectorProxy $group) use ($db) {
    $group->put('/human/add', function ($request, $response, $args) use ($db) {

        $payload = $request->getParsedBody();

        if(!isset($payload['human'])) {
            $errorMessage = json_encode(["message" => "The request must contain payload with human data"]);
            $response->getBody()->write($errorMessage);

            return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
        }

        $validator = Validation::createValidator();

        $humanConstraints = new Assert\Collection([
            'fields' => [
                'firstname' => new Assert\NotBlank(['message' => 'The field firstname for human cannot be blank.']),
                'lastname' => new Assert\NotBlank(['message' => 'The field lastname for human cannot be blank.']),
                'fullname' => new Assert\Optional([new Assert\NotBlank(['message' => 'The field fullname for human cannot be blank if present.'])]),
                'alias' => new Assert\Optional([new Assert\NotBlank(['message' => 'The field alias for human cannot be blank if present.'])]),
                'gender' => [new Assert\NotBlank(['message' => 'The field gender for human cannot be blank']), new Assert\Choice(['choices' => ['m', 'f']])],	
                'birth_date' => [new Assert\NotBlank(), new Assert\Date(['message' => 'The field birth_date for human must be a valid date.'])],
                'death_date' => new Assert\Optional([new Assert\Date(['message' => 'The field death_date for human must be a valid date if present.'])]),
                'country_code' => new Assert\Optional([new Assert\NotBlank(['message' => 'The field country_code for human cannot be blank if present.'])]),
                'sex_orientation' => new Assert\Optional([new Assert\NotBlank(['message' => 'The field sex_orientation for human cannot be blank if present.'])]),
                'religion' => new Assert\Optional([new Assert\NotBlank(['message' => 'The field religion for human cannot be blank if present.'])]),
                'category' => new Assert\Optional([
                    new Assert\NotBlank(['message' => 'The field category for human cannot be blank if present.']),
                    new Assert\Choice(['choices' => [CATEGORY_CODE_ACTOR, CATEGORY_CODE_MUSICIAN, CATEGORY_CODE_FAMILY], 'message' => 'The field category for human must be either ACTOR, MUSICIAN or FAMILY.'])
                ])
            ],
            'allowExtraFields' => false, // Povolit extra fieldy
            'missingFieldsMessage' => 'The field {{ field }} for human is required.',
            'extraFieldsMessage' => 'Unknown field(s) found for human: {{ field }}',
        ]);

        $violations = $validator->validate($payload['human'], $humanConstraints);
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errorMessage = json_encode(["message" => $violation->getMessage()]);
                $response->getBody()->write($errorMessage);

                return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(422);
            }
        }

        if(isset($payload['physicalAttributes'])) {
            $physicalAttributesContraints = new Assert\Collection([
                'fields' => [
                    'height' => new Assert\Optional([new Assert\NotBlank(['message' => 'The field height for physical attributes cannot be blank if present.'])]),
                    'weight' => new Assert\Optional([new Assert\NotBlank(['message' => 'The field weight for physical attributes cannot be blank if present.'])]),
                    'eyes_color' => new Assert\Optional([new Assert\NotBlank(['message' => 'The field eyes_color for physical attributes cannot be blank if present.'])]),
                    'hair_color' => new Assert\Optional([new Assert\NotBlank(['message' => 'The field hair_color for physical attributes cannot be blank if present.'])]),
                ],
                'allowExtraFields' => false, // povolit extra fieldy
                'extraFieldsMessage' => 'Unknown field(s) found for physical attributes: {{ field }}',
            ]);

            $violations = $validator->validate($payload['physicalAttributes'], $physicalAttributesContraints);
            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $errorMessage = json_encode(["message" => $violation->getMessage()]);
                    $response->getBody()->write($errorMessage);

                    return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(422);
                }
            }
        }

        if(isset($payload['socialAttributes'])) {
            $socialAttributesContraints = new Assert\Collection([
                'fields' => [
                    'siblings' => new Assert\Optional([new Assert\NotBlank(['message' => 'The field siblings for social attributes cannot be blank if present.'])]),
                    'children' => new Assert\Optional([new Assert\NotBlank(['message' => 'The field children for social attributes cannot be blank if present.'])]),
                    'marriages' => new Assert\Optional([new Assert\NotBlank(['message' => 'The field marriages for social attributes cannot be blank if present.'])]),
                ],
                'allowExtraFields' => false, // povolit extra fieldy
                'extraFieldsMessage' => 'Unknown field(s) found for social attributes: {{ field }}',
            ]);

            $violations = $validator->validate($payload['socialAttributes'], $socialAttributesContraints);
            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $errorMessage = json_encode(["message" => $violation->getMessage()]);
                    $response->getBody()->write($errorMessage);

                    return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(422);
                }
            }
        }

        if(isset($payload['contacts'])) {
            $constraints = new Assert\All([
                new Assert\Collection([
                    'fields' => [
                        'type' => [
                            new Assert\NotBlank(['message' => 'The field type for contacts cannot be blank.']), 
                            new Assert\Choice(['choices' => [CONTACT_TYPE_EMAIL, CONTACT_TYPE_PHONE], 'message' => 'The field type for contacts must be either EMAIL or PHONE.'])
                        ],
                        'value' => new Assert\NotBlank(['message' => 'The field value for contacts cannot be blank.']),
                    ],
                    'allowExtraFields' => false, // povolit extra fieldy
                    'missingFieldsMessage' => 'The field {{ field }} for contacts is required.',
                    'extraFieldsMessage' => 'Unknown field(s) found for contacts: {{ field }}',
                ])
            ]);

            $violations = $validator->validate($payload['contacts'], $constraints);
            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $errorMessage = json_encode(["message" => $violation->getMessage()]);
                    $response->getBody()->write($errorMessage);

                    return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(422);
                }
            }
        }


        if(isset($payload['human']['category'])) {
            $category = $db->query('SELECT id FROM humans_categories_types WHERE code = %s', $payload['human']['category'])->fetch();
            $payload['human']['category'] = $category->id;
        }
        
        $db->query('INSERT INTO humans %v', $payload['human']);
        $human_id = $db->getInsertId();

        $payload['physicalAttributes']['human_id'] = $human_id;
        $db->query('INSERT INTO humans_physical_attributes %v', $payload['physicalAttributes']);

        $payload['socialAttributes']['human_id'] = $human_id;
        $db->query('INSERT INTO humans_social_attributes %v', $payload['socialAttributes']);

        if(isset($payload['contacts'])) {
            // Get contacts types (EMAIL, PHONE)
            $contactsTypes = $db->query('SELECT * FROM humans_contacts_types');

            while($row = $contactsTypes->fetch()) {
                $contactsTypesArray[$row->code] = $row->id;
            }

            foreach($payload['contacts'] as $contact) {
                $contact['human_id'] = $human_id;
                $contact['type'] = $contactsTypesArray[$contact['type']];

                $db->query('INSERT INTO humans_contacts %v', $contact);
            }
        }

        $payload = json_encode(["message" => "Human created successfully."]);
        $response->getBody()->write($payload);
        
        return $response
              ->withHeader('Content-Type', 'application/json')
              ->withStatus(201);
    });
});

$app->run();