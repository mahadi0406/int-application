<?php

namespace Debugsolver\Bappe\Controller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{

    public function index()
    {
        $domain = $_SERVER['SERVER_NAME'];
        $data = $this->setDomain($domain);
        return view('indoc::install');
    }

    public function requirement()
    {
    	$extensionArrayList = $this->checkRequirement();
        return view('indoc::requirement', compact('extensionArrayList'));
    }

    public function permissionsCheck()
    {
        $checkExtensions = $this->checkRequirement();
        if(!in_array(0, $checkExtensions)) {
            $chekPermissions = $this->checkPermissions();
            return view('indoc::permissions', compact('chekPermissions'));
        }
        return redirect()->route('installer');
    }

    public function purchasedCode()
    {
    	$chekPermissions = $this->checkPermissions();
        if(isset($chekPermissions['grantPermission']) && $chekPermissions['grantPermission'] == 1) {
            return view('indoc::purchased_code');
        }
        return redirect()->route('installer');
    }

    public function purchasedCodeStore(Request $request)
    {
    	$this->validate($request, [
            'purchased_code' => 'required|min:5',
            'email' => 'required|email|max:150',
            'database_host' => 'required|min:5',
            'database_port' => 'required|min:2',
            'database_name' => 'required|min:1',
            'database_username' => 'required|min:1',
            'database_password' => 'nullable|min:5'
        ]);
        $dataArray['purchased_code'] = $request->purchased_code;
        $dataArray['buyer_domain'] = $_SERVER['SERVER_NAME'];
        $getData = json_decode($this->processURL($dataArray));
        if(!$getData->status){
            return back()->with('error', $getData->message)->withInput();
        }elseif($getData->status){
            $database = [
    			'DB_HOST' => $request->database_host,
    			'DB_PORT' => $request->database_port,
    			'DB_DATABASE' => $request->database_name,
    			'DB_USERNAME' => $request->database_username,
    			'DB_PASSWORD' => $request->database_password ?? '',
            ];
            config(['xsenderinfo.unique_id' => $getData->data->buyer_unique_id]);
            $fp = fopen(base_path() . '/config/xsenderinfo.php', 'w');
            fwrite($fp, '<?php return ' . var_export(config('xsenderinfo'), true) . ';');
            fclose($fp);
            $this->setEnv($database);
            try{
                $path = (resource_path('database/') . 'database.sql');
                DB::unprepared(file_get_contents($path));
            }catch (Exception $e){
                $error= "Could not connect to the database.  Please check your database configuration.";
                return back()->with('error', $error)->withInput();
            }
        }
        return view('indoc::complete');
    }

    private function fileCreate($data)
    {
        $installedLogFile = storage_path('logins');
        if(!file_exists($installedLogFile)){
            file_put_contents($installedLogFile, base64_encode($data));
        }
    }
    
    private function processURL($dataArray){
        $ch = curl_init();
        $url = "https://license.igensolutionsltd.com";
        $data = http_build_query($dataArray);
        $getUrl = $url."?".$data;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $getUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function setDomain($domain){
        $ch = curl_init();
        $url = "https://license.igensolutionsltd.com";
        $dataArray = ['buyer_domain' => $domain];
        $data = http_build_query($dataArray);
        $getUrl = $url."?".$data;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $getUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function setEnv($value)
    {
        $envPath = base_path('.env');
        $env = file($envPath);
        foreach ($env as $env_key => $env_value) {
            $entry = explode("=", $env_value, 2);
            if (array_key_exists($entry[0], $value)) {
                $env[$env_key] = $entry[0] . "=" . $value[$entry[0]] . "\n";
            } else {
                $env[$env_key] = $env_value;
            }
        }
        $fp = fopen($envPath, 'w');
        fwrite($fp, implode($env));
        fclose($fp);
    }

    private function checkRequirement()
    {
    	$extensions = ['openssl','gd', 'json', 'mbstring', 'PDO','bcmath', 'ctype', 'fileinfo', 'tokenizer', 'xml'];
        $requireVersion = '7.4.0';
        $extensionArrayList = [];
        $extensionArrayList["Required PHP version $requireVersion"] = in_array(version_compare($requireVersion, phpversion()), [-1, 0]) ? 1 : 0;
        foreach ($extensions as $extension) {
            $extensionArrayList[$extension] = extension_loaded($extension) ? 1 : 0;
        }
        return $extensionArrayList;
    }

    public function checkPermissions()
    {
        $exts = [];
        $grantPermission = 1;
        $direcotries = [
            'bootstrap/cache' => '775',
            'storage/app' => '775',
            'storage/framework' => '775',
            'storage/logs' => '775',
            '.env' => '777',
        ];
        foreach ($direcotries as $key => $directory){
            $path = '../'.$key;
            $oct = sprintf("%04d", $directory);
            $permission = substr(sprintf('%o', fileperms($path)), -4);;
            $exts[$key] = ['required' => $oct, 'permission' => $permission, 'value' => (octdec($permission) >= octdec($oct) ? 1 : 0)];
            $grantPermission = ($grantPermission == 0 || $exts[$key]['value'] == 0) ? 0 : 1;
        }
        return compact('exts', 'grantPermission');
    }
}
