<?php

namespace Heureka\Models;

use Heureka\Repositories\OrderRepository;
use HeurekaDeps\Wpify\Model\Order;

/**
 * @method OrderRepository model_repository()
 */
class OrderModel extends Order {

	public int $_heureka_id;

}
