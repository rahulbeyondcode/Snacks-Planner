<?php

return [
    'money_pool_id_required' => 'The money pool ID field is required.',
    'money_pool_id_integer' => 'The money pool ID must be an integer.',
    'money_pool_id_exists' => 'The selected money pool does not exist.',
    'amount_required' => 'The amount field is required.',
    'amount_numeric' => 'The amount must be a number.',
    'amount_min' => 'The amount must be at least 0.01.',
    'amount_max' => 'The amount may not be greater than 999,999.99.',
    'reason_required' => 'The reason field is required.',
    'reason_string' => 'The reason must be a string.',
    'reason_max' => 'The reason may not be greater than 500 characters.',
    'block_date_required' => 'The block date field is required.',
    'block_date_date' => 'The block date must be a valid date.',
    'block_date_after_or_equal' => 'The block date must be within the current month.',
    'block_date_before_or_equal' => 'The block date must be within the current month.',
    'block_not_found' => 'Money pool block not found.',
];
