<?php
$installer = $this;
$installer->AddAttribute(
    'quote_payment',
    'digitalcheck_cc_division_times',
    array(
        'visible'=> false
    )
);
$installer->AddAttribute(
    'order_payment',
    'digitalcheck_cc_division_times',
    array(
        'visible'=> false
    )
);
$installer->AddAttribute(
        'order_payment',
        'dc_cvs',
        array(
            'visible'=> false
            )
        );
$installer->AddAttribute(
        'quote_payment',
        'dc_cvs',
        array(
            'visible'=> false
            )
        );
$installer->AddAttribute(
        'order_payment',
        'cvs_receipt_url',
        array(
            'visible'=> false
            )
        );
$installer->AddAttribute(
        'quote_payment',
        'cvs_receipt_url',
        array(
            'visible'=> false
            )
        );
$installer->AddAttribute(
        'order_payment',
        'pe_receipt_url',
        array(
            'visible'=> false
            )
        );

$installer->AddAttribute(
        'quote_payment',
        'pe_receipt_url',
        array(
            'visible'=> false
            )
        );
$installer->AddAttribute(
        'order_payment',
        'pay_limit_date',
        array(
            'visible'=> false
            )
        );
$installer->AddAttribute(
        'quote_payment',
        'pay_limit_date',
        array(
            'visible'=> false
            )
        );
$installer->AddAttribute(
        'order_payment',
        'cvs_receipt_number',
        array(
            'visible'=> false
            )
        );
$installer->AddAttribute(
        'quote_payment',
        'cvs_receipt_number',
        array(
            'visible'=> false
            )
        );
$installer->AddAttribute(
        'order_payment',
        'pe_receipt_number',
        array(
            'visible'=> false
            )
        );
$installer->AddAttribute(
        'quote_payment',
        'pe_receipt_number',
        array(
            'visible'=> false
            )
        );

