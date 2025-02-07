<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(__DIR__);
$baseDir = dirname($vendorDir);

return array(
    'Composer\\InstalledVersions' => $vendorDir . '/composer/InstalledVersions.php',
    'Heureka\\Abstracts\\AbstractFeed' => $baseDir . '/src/Abstracts/AbstractFeed.php',
    'Heureka\\Abstracts\\AbstractRequest' => $baseDir . '/src/Abstracts/AbstractRequest.php',
    'Heureka\\Abstracts\\AbstractSettings' => $baseDir . '/src/Abstracts/AbstractSettings.php',
    'Heureka\\Api\\OrderApi' => $baseDir . '/src/Api/OrderApi.php',
    'Heureka\\Api\\PaymentApi' => $baseDir . '/src/Api/PaymentApi.php',
    'Heureka\\Api\\ProductsApi' => $baseDir . '/src/Api/ProductsApi.php',
    'Heureka\\CLI' => $baseDir . '/src/CLI.php',
    'Heureka\\Controllers\\ProductItemIdController' => $baseDir . '/src/Controllers/ProductItemIdController.php',
    'Heureka\\Enums\\DeliveryMethods' => $baseDir . '/src/Enums/DeliveryMethods.php',
    'Heureka\\Enums\\DeliveryTypes' => $baseDir . '/src/Enums/DeliveryTypes.php',
    'Heureka\\Enums\\OrderStatuses' => $baseDir . '/src/Enums/OrderStatuses.php',
    'Heureka\\Enums\\PaymentStatuses' => $baseDir . '/src/Enums/PaymentStatuses.php',
    'Heureka\\Enums\\PaymentTypes' => $baseDir . '/src/Enums/PaymentTypes.php',
    'Heureka\\Enums\\StoreTypes' => $baseDir . '/src/Enums/StoreTypes.php',
    'Heureka\\Enums\\WeekDays' => $baseDir . '/src/Enums/WeekDays.php',
    'Heureka\\Feed\\FeedAvailability' => $baseDir . '/src/Feed/FeedAvailability.php',
    'Heureka\\Feed\\FeedCategories' => $baseDir . '/src/Feed/FeedCategories.php',
    'Heureka\\Feed\\FeedProduct' => $baseDir . '/src/Feed/FeedProduct.php',
    'Heureka\\Feed\\FeedProductCz' => $baseDir . '/src/Feed/FeedProductCz.php',
    'Heureka\\Feed\\FeedProductSk' => $baseDir . '/src/Feed/FeedProductSk.php',
    'Heureka\\Helpers\\IpInRange' => $baseDir . '/src/Helpers/IpInRange.php',
    'Heureka\\HeurekaApi\\In\\OrderCancel' => $baseDir . '/src/HeurekaApi/In/OrderCancel.php',
    'Heureka\\HeurekaApi\\In\\OrderSend' => $baseDir . '/src/HeurekaApi/In/OrderSend.php',
    'Heureka\\HeurekaApi\\In\\OrderStatus' => $baseDir . '/src/HeurekaApi/In/OrderStatus.php',
    'Heureka\\HeurekaApi\\In\\PaymentDelivery' => $baseDir . '/src/HeurekaApi/In/PaymentDelivery.php',
    'Heureka\\HeurekaApi\\In\\PaymentStatus' => $baseDir . '/src/HeurekaApi/In/PaymentStatus.php',
    'Heureka\\HeurekaApi\\In\\ProductsAvailability' => $baseDir . '/src/HeurekaApi/In/ProductsAvailability.php',
    'Heureka\\HeurekaApi\\Out\\GetOrderStatus' => $baseDir . '/src/HeurekaApi/Out/GetOrderStatus.php',
    'Heureka\\HeurekaApi\\Out\\GetPaymentStatus' => $baseDir . '/src/HeurekaApi/Out/GetPaymentStatus.php',
    'Heureka\\HeurekaApi\\Out\\GetShopStatus' => $baseDir . '/src/HeurekaApi/Out/GetShopStatus.php',
    'Heureka\\HeurekaApi\\Out\\GetStores' => $baseDir . '/src/HeurekaApi/Out/GetStores.php',
    'Heureka\\HeurekaApi\\Out\\PostOrderInvoice' => $baseDir . '/src/HeurekaApi/Out/PostOrderInvoice.php',
    'Heureka\\HeurekaApi\\Out\\PostOrderNote' => $baseDir . '/src/HeurekaApi/Out/PostOrderNote.php',
    'Heureka\\HeurekaApi\\Out\\PutOrderStatus' => $baseDir . '/src/HeurekaApi/Out/PutOrderStatus.php',
    'Heureka\\HeurekaApi\\Out\\PutPaymentStatus' => $baseDir . '/src/HeurekaApi/Out/PutPaymentStatus.php',
    'Heureka\\Managers\\ApiManager' => $baseDir . '/src/Managers/ApiManager.php',
    'Heureka\\Managers\\FeedsManager' => $baseDir . '/src/Managers/FeedsManager.php',
    'Heureka\\Managers\\HelpersManager' => $baseDir . '/src/Managers/HelpersManager.php',
    'Heureka\\Managers\\PostTypesManager' => $baseDir . '/src/Managers/PostTypesManager.php',
    'Heureka\\Models\\OrderModel' => $baseDir . '/src/Models/OrderModel.php',
    'Heureka\\Models\\ProductItemIdModel' => $baseDir . '/src/Models/ProductItemIdModel.php',
    'Heureka\\Models\\ProductModel' => $baseDir . '/src/Models/ProductModel.php',
    'Heureka\\Plugin' => $baseDir . '/src/Plugin.php',
    'Heureka\\PostTypes\\ProductPostType' => $baseDir . '/src/PostTypes/ProductPostType.php',
    'Heureka\\Repositories\\OrderRepository' => $baseDir . '/src/Repositories/OrderRepository.php',
    'Heureka\\Repositories\\ProductItemIdRepository' => $baseDir . '/src/Repositories/ProductItemIdRepository.php',
    'Heureka\\Repositories\\ProductRepository' => $baseDir . '/src/Repositories/ProductRepository.php',
    'Heureka\\Repositories\\SettingsRepository' => $baseDir . '/src/Repositories/SettingsRepository.php',
    'Heureka\\Settings' => $baseDir . '/src/Settings.php',
    'Heureka\\Settings\\ConversionTrackingSettings' => $baseDir . '/src/Settings/ConversionTrackingSettings.php',
    'Heureka\\Settings\\CustomerVerifiedSettings' => $baseDir . '/src/Settings/CustomerVerifiedSettings.php',
    'Heureka\\Settings\\FeedProductsCzSettings' => $baseDir . '/src/Settings/FeedProductsCzSettings.php',
    'Heureka\\Settings\\FeedProductsSettings' => $baseDir . '/src/Settings/FeedProductsSettings.php',
    'Heureka\\Settings\\FeedProductsSkSettings' => $baseDir . '/src/Settings/FeedProductsSkSettings.php',
    'Heureka\\Settings\\GeneralSettings' => $baseDir . '/src/Settings/GeneralSettings.php',
    'Heureka\\Settings\\MarketplaceSettings' => $baseDir . '/src/Settings/MarketplaceSettings.php',
    'Heureka\\Settings\\ProductSettings' => $baseDir . '/src/Settings/ProductSettings.php',
    'Heureka\\Traits\\ProductDataTrait' => $baseDir . '/src/Traits/ProductDataTrait.php',
    'Heureka\\Woocommerce' => $baseDir . '/src/Woocommerce.php',
    'Heureka\\WpRequester' => $baseDir . '/src/WpRequester.php',
);
