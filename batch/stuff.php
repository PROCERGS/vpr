<?php
require_once '../vendor/autoload.php';

function checkArguments($argv)
{
    if (!isset($argv[1])) {
        e("Missing config file, private key or passphrase");
        exit(1);
    }

    $config = loadConfigFromFile($argv[1]);

    $privateKeyFile = realpath($config['private_key_file']);
    if (!file_exists($privateKeyFile)) {
        e("Private key file not found!");
        exit(1);
    }

    $config['private_key_file'] = $privateKeyFile;
    return $config;
}

function loadConfigFromFile($file)
{
    $config = parse_ini_file($file);
    if (!$config) {
        e("Couldn't parse the config file.");
        exit(1);
    }
    return $config;
}

function getObviouslyValidVotesQuery()
{
    $sql = <<<SQL
SELECT
    *
FROM
    (SELECT
        v.*
    FROM
        vote v
        INNER JOIN tre_voter_corede vc ON vc.id = v.voter_registration
    WHERE
        vc.corede_id = v.corede_id
    ) valid_corede
GROUP BY
    valid_corede.voter_registration
HAVING
    COUNT(valid_corede.voter_registration) = 1;
SQL;
    return $sql;
}

function getPDOConnection($config)
{
    $db_driver = $config['database_driver'];
    $db_host   = $config['database_host'];
    $db_name   = $config['database_name'];
    $db_user   = $config['database_user'];
    $db_pass   = $config['database_password'];
    $pdo       = new PDO("$db_driver:host=$db_host;dbname=$db_name", $db_user,
        $db_pass
    );
    $pdo->query("SET NAMES 'UTF8'");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

function e($message, $breakLinke = true)
{
    echo $message.($breakLinke ? PHP_EOL : '');
}

function signVote($vote, $config)
{
    $privateKey = $config['pollPrivateKey'];
    $signature  = false;
    openssl_sign(json_encode($vote), $signature, $privateKey);

    return base64_encode($signature);
}

function getInsertOpenVoteQuery()
{
    return 'INSERT INTO open_vote (ballot_box_id, corede_id, poll_option_id, signature, auth_type, voter_registration, city_id, salt) VALUES (:ballot_box_id, :corede_id, :poll_option_id, :signature, :auth_type, :voter_registration, :city_id, :salt)';
}

function getInsertOpenBallotQuery()
{
    return 'INSERT INTO open_ballot (`ballot_box_id`, `corede_id`, `options`, `created_at`, `signature`, `auth_type`, `has_nfg`, `has_voter_registration`, `has_citizen_login`, `wb_treatment_vpr`, `wb_treatment_gabinete_digital`, `surveymonkey_id`) VALUE (:ballot_box_id, :corede_id, :options, :created_at, :signature, :auth_type, :has_nfg, :has_voter_registration, :has_citizen_login, :wb_treatment_vpr, :wb_treatment_gabinete_digital, :surveymonkey_id)';
}

function getAllValidVotesQuery()
{
    $sql = <<<SQL
SELECT
    *
FROM
    (SELECT
        v.*
    FROM
        vote v
        INNER JOIN tre_voter_corede vc ON vc.id = v.voter_registration
    WHERE
        vc.corede_id = v.corede_id
    ) valid_corede
GROUP BY
    valid_corede.voter_registration
HAVING
    COUNT(valid_corede.voter_registration) = 1
UNION ALL
SELECT * FROM
(
	SELECT
		v.*
	FROM
		vote v
		INNER JOIN conflicting_voter_registration c ON c.voter_registration = v.voter_registration
	ORDER BY
		v.voter_registration DESC,
		v.nfg_cpf DESC,
		v.login_cidadao_id DESC,
		v.created_at DESC
) x
GROUP BY
	x.voter_registration;
SQL;
    return $sql;
}

function createOpenVote($optionId, $coredeId, $config, $row)
{
    $openVote                       = array();
    $openVote['ballot_box_id']      = $row['ballot_box_id'];
    $openVote['corede_id']          = $coredeId;
    $openVote['city_id']            = $row['city_id'];
    $openVote['poll_option_id']     = $optionId;
    $openVote['auth_type']          = $row['auth_type'];
    $openVote['voter_registration'] = $row['voter_registration'];
    $openVote['signature']          = null;
    $openVote['salt']               = Rhumsaa\Uuid\Uuid::uuid4()->toString();
    $openVote['signature']          = signVote($openVote, $config);
    return $openVote;
}
