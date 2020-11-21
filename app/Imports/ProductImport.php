<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Product;

class ProductImport implements ToCollection, WithCalculatedFormulas
{
    /**
    * @param Collection $collection
    */

    private $data;
    private $res = array();
    private $header = array(
        'product'=>'product_name',
        'product name'=>'product_name',
        'location id'=>'room_id',
        'room id'=>'room_id',
        'qty'=>'qty',
        'manufacturer'=>'manufacturer',
        'model number'=>'model_number',
        'model'=>'model_number',
        'description'=>'description',
        'testing id'=>'test_form_id',
        'commissioning id'=>'com_form_id',
        'commisioning id'=>'com_form_id',
        'action'=>'action',
        'product_action'=>'action'
    );
    private $header_set = array('product_name', 'room_id', 'qty', 'manufacturer',
        'model_number', 'description', 'test_form_id', 'com_form_id', 'action');

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection(Collection $collection)
    {
        $idx = 0;
        $cnt = 0;
        $product = array();
        $value_id = array();

        foreach($collection as $row) {
            $idx ++;
            if ($idx == 1) {
                $sum = 0; $s = 0;
                for ($i = 0; $i < 9 && $i < count($row); $i++) {
                    $srow = strtolower($row[$i]);
                    if (array_key_exists($srow, $this->header)) {
                        $frow = $this->header[$srow];
                        $s = array_search($frow, $this->header_set);
                        $sum |= 1<<$s;
                        $value_id[$i] = $frow;
                    } else {
                        break;
                    }
                }
                if ($i < 9 || $sum != 511) {
                    $this->res['status'] = "error";
                    $this->res['msg'] = 'The excel format is not correct.' . $i . ", " . $sum;
                    return ;
                }
                continue;
            }
            if ($row[0] == '')
                continue;
            for ($i = 0; $i < 9; $i++) {
                $val = $row[$i];
                if ($value_id[$i] == 'test_form_id' || $value_id[$i] == 'com_form_id') {
                    if (!is_numeric($val)) {
                        $val = "0";
                    }
                }
                if ($value_id[$i] == 'action') {
                    $product['action'] = 0;
                    if (strtolower($val) == 'new product') {
                        $product['action'] = 0;
                    } else if (strtolower($val) == 'dispose') {
                        $product['action'] = 1;
                    } else if (strtolower($val) == 'move to room') {
                        $product['action'] = 2;
                    }
                } else {
                    $product[$value_id[$i]] = $val;
                }
            }
            $product['signed_off'] = 0;
            $product['created_by']  = $this->data;

            Product::create($product);
            $cnt ++;
        }
        $this->res['total'] = $idx - 1;
        $this->res['cnt'] = $cnt;
        $this->res['status'] = "success";
    }

    public function get_value() {
        return $this->res;
    }
}
