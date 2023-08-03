<?php
namespace Modules\Account\Classes;

use Illuminate\Support\Facades\Cache;
use Modules\Account\Entities\Gateway as DBGateway;

class Gateway
{
    /**
     * Get Gateways List
     *
     * @param bool  $show_hidden
     * @param bool $invoice
     *
     * @return array
     */
    public function getGateways($show_hidden = false, $invoice = false)
    {
        $gateways_qry = DBGateway::where('published', true);

        if (!$show_hidden) {
            $gateways_qry->where('is_hidden', false)
                ->orderBy('ordering', 'DESC')
                ->orderBy('id', 'ASC');
        }

        $gateways = $gateways_qry->get();

        foreach ($gateways as $key => $gateway) {

            $class_name = $this->getClassName($gateway->module);

            if ($invoice) {
                $gateway->tabs = $class_name->getGatewayTab($gateway, $invoice);
            }

            if (!isset($gateway->tabs)) {
                $gateway->tabs = [];
            }
        }

        return $gateways;

    }

    /**
     * Get Gateway
     *
     * @param int $gateway_id
     *
     * @return object|bool
     */
    public function getGateway($gateway_id) 
    {
        if (Cache::has("account_gateway_" . $gateway_id)) {
            $gateway = Cache::get("account_gateway_" . $gateway_id);
            return $gateway;
        } else {
            try {
                $gateway = DBGateway::where('id', $gateway_id)->first();

                Cache::put("account_gateway_" . $gateway_id, $gateway, 3600);
                //code...
                return $gateway;
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        return false;
    }

    /**
     * Get Gateway by ID
     *
     * @param int $gateway_id
     *
     * @return object|bool
     */
    public function getGatewayById($gateway_id)
    {
        return $this->getGateway($gateway_id);
    }

    /**
     * Get Gateway by Slug
     *
     * @param string $gateway_slug
     *
     * @return array|bool
     */
    public function getGatewayBySlug($gateway_slug) 
    {
        $gateway = DBGateway::where('slug', $gateway_slug)->first();

        if (Cache::has("account_gateway_" . $gateway_slug)) {
            $gateway = Cache::get("account_gateway_" . $gateway_slug);
            return $gateway;
        } else {
            try {
                $gateway = DBGateway::where('slug', $gateway_slug)->first();

                Cache::put("account_gateway_" . $gateway_slug, $gateway, 3600);

                return $gateway;
            } catch (\Throwable $th) {
                throw $th;
            }

            return false;
        }
    }

    /**
     * Get Gateway ID
     *
     * @param string $gateway_slug
     *
     * @return array|bool
     */
    public function getGatewayId($gateway_slug)
    {
        if (Cache::has("account_gateway_" . $gateway_slug . "_id")) {
            $gateway_id = Cache::get("account_gateway_" . $gateway_slug . "_id");
            return $gateway_id;
        } else {
            try {
                $gateway = DBGateway::where('slug', $gateway_slug)->first();
                $gateway_id = $gateway->id;
                Cache::put("account_gateway_" . $gateway_slug . "_id", $gateway->id, 3600);

                return $gateway_id;
                //code...
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        return false;
    }
 
    /**
     * Get Class Name
     *
     * @param string $module
     *
     * @return mixed
     */
    private function getClassName($module)
    {
        $classname = 'Modules\\' . ucfirst($module) . '\Classes\Gateway';

        if (class_exists($classname)) {
            return new $classname();
        } else {
            throw new \Exception("class $classname not found.", 1);

        };
    }

}
