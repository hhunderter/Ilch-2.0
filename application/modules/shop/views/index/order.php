<?php
$itemsMapper = $this->get('itemsMapper');
// Checking this here doesn't really add any security but we are trying to avoid showing the customer an order without a delivery fee
// when it might not be possible. The final check is done in the controller and an order is aborted if will collect is selected
// and the feature disabled.
$willCollectAllowedAndChecked = $this->get('allowWillCollect') && isset($_SESSION['shopping_willCollect']);

/* show shopcart */
$cart_badge = '';
if (!empty($_SESSION['shopping_cart'])) {
    $cart_count = count(array_keys($_SESSION['shopping_cart']));
    $cart_badge = ($cart_count > 0) ? '<a class="activecart" href="' . $this->getUrl('shop/index/cart') . '#shopAnker">' . $this->getTrans('menuCart') . '<i class="fa-solid fa-shopping-cart"><span class="badge">' . $cart_count . '</span></i></a>' : '';
}
?>

<h1>
    <?=$this->getTrans('menuOrder') ?>
    <?=$cart_badge ?>
    <div id="shopAnker"></div>
</h1>

<?php if (isset($_SESSION['shopping_cart'])) {
    $total_price = 0; ?>

    <?php if ($this->getUser() !== null) { ?>
        <h4><?=$this->getTrans('hello') ?> <b><?=$this->escape($this->getUser()->getName()) ?></b></h4>
        <p><?=$this->getTrans('infoFormOrder') ?></p>
        <?php if ($willCollectAllowedAndChecked) : ?>
        <p><?=$this->getTrans('infoFormOrderWillCollect') ?></p>
        <?php endif; ?>

        <form class="order" action="#shopAnker" method="POST">
            <?=$this->getTokenField() ?>
            <?php $order = str_replace('"', "'", json_encode($_SESSION['shopping_cart'])); ?>
            <input type="hidden" name="order" value="<?=$order ?>" />
            <input type="hidden" name="willCollect" value="<?=($willCollectAllowedAndChecked) ? 1 : 0 ?>" />
            <input type="checkbox" id="differentInvoiceAddress" name="differentInvoiceAddress" <?=($this->originalInput('differentInvoiceAddress') != '' ? 'checked' : '') ?> />
            <label for="differentInvoiceAddress"><?=$this->getTrans('differentInvoiceAddress') ?></label>

            <div class="row space20"></div>
            <h4><?=$this->getTrans('infoBuyer') ?></h4>
            <div class="row space20"></div>

            <?=$this->getTrans('deliveryAddress') ?>
            <div class="row space20"></div>

            <div id="deliveryAddress">
                <?php if ($this->get('addresses')) : ?>
                <div class="row mb-3<?=$this->validation()->hasError('dropdownDeliveryAddress') ? ' has-error' : '' ?>">
                    <label for="dropdownDeliveryAddress" class="col-form-label col-xl-2">
                        <?=$this->getTrans('dropdownDeliveryAddress') ?>
                    </label>
                    <div class="col-xl-9">
                        <select class="form-select" id="dropdownDeliveryAddress" name="dropdownDeliveryAddress" data-placeholder="<?=$this->getTrans('selectAddress') ?>">
                            <option value=""><?=$this->getTrans('selectAddress') ?></option>
                            <?php foreach ($this->get('addresses') as $address) : ?>
                                <option value="<?=$address->getId() ?>"><?=$this->escape(sprintf('%s, %s (%s, %s %s, %s)', $address->getPrename(), $address->getLastname(), $address->getStreet(), $address->getPostcode(), $address->getCity(), $address->getCountry())) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row mb-3<?=$this->validation()->hasError('prename') ? ' has-error' : '' ?>">
                    <label for="prename" class="col-form-label col-xl-2">
                        <?=$this->getTrans('preName') ?>&nbsp;*
                    </label>
                    <div class="col-xl-9">
                        <input type="text"
                               class="form-control"
                               id="prename"
                               name="prename"
                               placeholder="<?=$this->getTrans('preName') ?>"
                               value="<?=($this->originalInput('prename') != '' ? $this->escape($this->originalInput('prename')) : '') ?>" />
                    </div>
                </div>

                <div class="row mb-3<?=$this->validation()->hasError('lastname') ? ' has-error' : '' ?>">
                    <label for="lastname" class="col-form-label col-xl-2">
                        <?=$this->getTrans('lastName') ?>&nbsp;*
                    </label>
                    <div class="col-xl-9">
                        <input type="text"
                               class="form-control"
                               id="lastname"
                               name="lastname"
                               placeholder="<?=$this->getTrans('lastName') ?>"
                               value="<?=($this->originalInput('lastname') != '' ? $this->escape($this->originalInput('lastname')) : '') ?>" />
                    </div>
                </div>

                <div class="row mb-3<?=$this->validation()->hasError('street') ? ' has-error' : '' ?>">
                    <label for="street" class="col-form-label col-xl-2">
                        <?=$this->getTrans('street') ?>&nbsp;*
                    </label>
                    <div class="col-xl-9">
                        <input type="text"
                               class="form-control"
                               id="street"
                               name="street"
                               placeholder="<?=$this->getTrans('street') ?>"
                               value="<?=($this->originalInput('street') != '' ? $this->escape($this->originalInput('street')) : '') ?>" />
                    </div>
                </div>

                <div class="row mb-3<?=$this->validation()->hasError('postcode') ? ' has-error' : '' ?>">
                    <label for="postcode" class="col-form-label col-xl-2">
                        <?=$this->getTrans('postCode') ?>&nbsp;*
                    </label>
                    <div class="col-xl-9">
                        <input type="text"
                               class="form-control"
                               id="postcode"
                               name="postcode"
                               placeholder="<?=$this->getTrans('postCode') ?>"
                               value="<?=($this->originalInput('postcode') != '' ? $this->escape($this->originalInput('postcode')) : '') ?>" />
                    </div>
                </div>

                <div class="row mb-3<?=$this->validation()->hasError('city') ? ' has-error' : '' ?>">
                    <label for="city" class="col-form-label col-xl-2">
                        <?=$this->getTrans('city') ?>&nbsp;*
                    </label>
                    <div class="col-xl-9">
                        <input type="text"
                               class="form-control"
                               id="city"
                               name="city"
                               placeholder="<?=$this->getTrans('city') ?>"
                               value="<?=($this->originalInput('city') != '' ? $this->escape($this->originalInput('city')) : '') ?>" />
                    </div>
                </div>

                <div class="row mb-3<?=$this->validation()->hasError('country') ? ' has-error' : '' ?>">
                    <label for="country" class="col-form-label col-xl-2">
                        <?=$this->getTrans('country') ?>&nbsp;&nbsp;
                    </label>
                    <div class="col-xl-9">
                        <input type="text"
                               class="form-control"
                               id="country"
                               name="country"
                               placeholder="<?=$this->getTrans('country') ?>"
                               value="<?=($this->originalInput('country') != '' ? $this->escape($this->originalInput('country')) : '') ?>" />
                    </div>
                </div>

                <div class="row mb-3<?=$this->validation()->hasError('email') ? ' has-error' : '' ?>">
                    <label for="email" class="col-form-label col-xl-2">
                        <?=$this->getTrans('emailAddress') ?>&nbsp;*
                    </label>
                    <div class="col-xl-9">
                        <input type="text"
                               class="form-control"
                               id="email"
                               name="email"
                               placeholder="<?=$this->getTrans('emailAddress') ?>"
                               value="<?=$this->getUser()->getEmail() ?>"
                               readonly />
                    </div>
                </div>
            </div>

            <div id="invoiceAddress" style="display: none">
                <div class="row space20"></div>
                <?=$this->getTrans('invoiceAddress') ?>
                <div class="row space20"></div>

                <?php if ($this->get('addresses')) : ?>
                <div class="row mb-3<?=$this->validation()->hasError('dropdownInvoiceAddress') ? ' has-error' : '' ?>">
                    <label for="dropdownInvoiceAddress" class="col-form-label col-xl-2">
                        <?=$this->getTrans('dropdownInvoiceAddress') ?>
                    </label>
                    <div class="col-xl-9">
                        <select class="form-select" id="dropdownInvoiceAddress" name="dropdownInvoiceAddress" data-placeholder="<?=$this->getTrans('selectAddress') ?>">
                            <option value=""><?=$this->getTrans('selectAddress') ?></option>
                        <?php foreach ($this->get('addresses') as $address) : ?>
                            <option value="<?=$address->getId() ?>"><?=$this->escape(sprintf('%s, %s (%s, %s %s, %s)', $address->getPrename(), $address->getLastname(), $address->getStreet(), $address->getPostcode(), $address->getCity(), $address->getCountry())) ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row mb-3<?=$this->validation()->hasError('invoiceAddressPrename') ? ' has-error' : '' ?>">
                    <label for="invoiceAddressPrename" class="col-form-label col-xl-2">
                        <?=$this->getTrans('preName') ?>&nbsp;*
                    </label>
                    <div class="col-xl-9">
                        <input type="text"
                               class="form-control"
                               id="invoiceAddressPrename"
                               name="invoiceAddressPrename"
                               placeholder="<?=$this->getTrans('preName') ?>"
                               value="<?=($this->originalInput('invoiceAddressPrename') != '' ? $this->escape($this->originalInput('invoiceAddressPrename')) : '') ?>" />
                    </div>
                </div>

                <div class="row mb-3<?=$this->validation()->hasError('invoiceAddressLastname') ? ' has-error' : '' ?>">
                    <label for="invoiceAddressLastname" class="col-form-label col-xl-2">
                        <?=$this->getTrans('lastName') ?>&nbsp;*
                    </label>
                    <div class="col-xl-9">
                        <input type="text"
                               class="form-control"
                               id="invoiceAddressLastname"
                               name="invoiceAddressLastname"
                               placeholder="<?=$this->getTrans('lastName') ?>"
                               value="<?=($this->originalInput('invoiceAddressLastname') != '' ? $this->escape($this->originalInput('invoiceAddressLastname')) : '') ?>" />
                    </div>
                </div>

                <div class="row mb-3<?=$this->validation()->hasError('invoiceAddressStreet') ? ' has-error' : '' ?>">
                    <label for="invoiceAddressStreet" class="col-form-label col-xl-2">
                        <?=$this->getTrans('street') ?>&nbsp;*
                    </label>
                    <div class="col-xl-9">
                        <input type="text"
                               class="form-control"
                               id="invoiceAddressStreet"
                               name="invoiceAddressStreet"
                               placeholder="<?=$this->getTrans('street') ?>"
                               value="<?=($this->originalInput('invoiceAddressStreet') != '' ? $this->escape($this->originalInput('invoiceAddressStreet')) : '') ?>" />
                    </div>
                </div>

                <div class="row mb-3<?=$this->validation()->hasError('invoiceAddressPostcode') ? ' has-error' : '' ?>">
                    <label for="invoiceAddressPostcode" class="col-form-label col-xl-2">
                        <?=$this->getTrans('postCode') ?>&nbsp;*
                    </label>
                    <div class="col-xl-9">
                        <input type="text"
                               class="form-control"
                               id="invoiceAddressPostcode"
                               name="invoiceAddressPostcode"
                               placeholder="<?=$this->getTrans('postCode') ?>"
                               value="<?=($this->originalInput('invoiceAddressPostcode') != '' ? $this->escape($this->originalInput('invoiceAddressPostcode')) : '') ?>" />
                    </div>
                </div>

                <div class="row mb-3<?=$this->validation()->hasError('invoiceAddressCity') ? ' has-error' : '' ?>">
                    <label for="invoiceAddressCity" class="col-form-label col-xl-2">
                        <?=$this->getTrans('city') ?>&nbsp;*
                    </label>
                    <div class="col-xl-9">
                        <input type="text"
                               class="form-control"
                               id="invoiceAddressCity"
                               name="invoiceAddressCity"
                               placeholder="<?=$this->getTrans('city') ?>"
                               value="<?=($this->originalInput('invoiceAddressCity') != '' ? $this->escape($this->originalInput('invoiceAddressCity')) : '') ?>" />
                    </div>
                </div>

                <div class="row mb-3<?=$this->validation()->hasError('invoiceAddressCountry') ? ' has-error' : '' ?>">
                    <label for="invoiceAddressCountry" class="col-form-label col-xl-2">
                        <?=$this->getTrans('country') ?>&nbsp;&nbsp;
                    </label>
                    <div class="col-xl-9">
                        <input type="text"
                               class="form-control"
                               id="invoiceAddressCountry"
                               name="invoiceAddressCountry"
                               placeholder="<?=$this->getTrans('country') ?>"
                               value="<?=($this->originalInput('invoiceAddressCountry') != '' ? $this->escape($this->originalInput('invoiceAddressCountry')) : '') ?>" />
                    </div>
                </div>
            </div>

            <?php if ($this->get('captchaNeeded')) : ?>
                <div class="row mb-3<?=$this->validation()->hasError('captcha') ? ' has-error' : '' ?>">
                    <label class="col-xl-2 col-form-label">
                        <?=$this->getTrans('captcha') ?>&nbsp;*
                    </label>
                    <div class="col-xl-9">
                        <?=$this->getCaptchaField() ?>
                    </div>
                </div>
                <div class="row mb-3<?=$this->validation()->hasError('captcha') ? ' has-error' : '' ?>">
                    <div class="offset-xl-2 col-xl-9 input-group captcha">
                        <input type="text"
                               class="form-control"
                               id="captcha-form"
                               name="captcha"
                               autocomplete="off"
                               placeholder="<?=$this->getTrans('captcha') ?>" />
                        <span class="input-group-text">
                            <a href="javascript:void(0)" onclick="
                                document.getElementById('captcha').src='<?=$this->getUrl() ?>/application/libraries/Captcha/Captcha.php?'+Math.random();
                                document.getElementById('captcha-form').focus();"
                                id="change-image">
                                <i class="fa-solid fa-arrows-rotate"></i>
                            </a>
                        </span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row space20"></div>
            <h4><?=$this->getTrans('contentShopCart') ?></h4>
            <div class="row space20"></div>

            <div class="cart">
                <table>
                    <thead>
                        <tr>
                            <th scope="col" width="10%"><?=$this->getTrans('productImage') ?><br />&nbsp;</th>
                            <th scope="col" width="25%"><?=$this->getTrans('productName') ?><br /><small><?=$this->getTrans('itemNumber') ?></small></th>
                            <th scope="col" width="15%"><?=$this->getTrans('singlePrice') ?><br /><small><?=$this->getTrans('withoutTax') ?></small></th>
                            <th scope="col" width="10%"><?=$this->getTrans('taxShort') ?><br />&nbsp;</th>
                            <th scope="col" width="15%"><?=$this->getTrans('singlePrice') ?><br /><small><?=$this->getTrans('withTax') ?></small></th>
                            <th scope="col" width="10%" class="text-center"><?=$this->getTrans('entries') ?><br />&nbsp;</th>
                            <th scope="col" width="15%" class="text-end"><?=$this->getTrans('total') ?><br /><small><?=$this->getTrans('withTax') ?></small></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subtotal_price = 0;
                        foreach ($_SESSION['shopping_cart'] as $key => $product) {
                            $itemId = $product['id'];
                            $item = $itemsMapper->getShopItemById($itemId);

                            if (!$item) {
                                unset($_SESSION['shopping_cart'][$key]);
                                $_SESSION['messages'][] = ['text' => $this->getTrans('aProduct') . ' ' . $this->getTrans('removedFromCart') . ' ' . $this->getTrans('noLongerSold'), 'type' => 'danger'];
                                continue;
                            }

                            $itemImg = $item->getImage();
                            $itemName = $item->getName();
                            $itemNumber = $item->getItemnumber();
                            $itemPrice = $item->getPrice();
                            $itemTax = $item->getTax();
                            $itemPriceWithoutTax = round(($itemPrice / (100 + $itemTax)) * 100, 2);
                            $arrayShippingCosts[] = $item->getShippingCosts();
                            $arrayTaxes[] = $itemTax;
                            $arrayPrices[] = $itemPrice * $product['quantity'];
                            $arrayPricesWithoutTax[] = $itemPriceWithoutTax * $product['quantity'];
                            $shopImgPath = '/application/modules/shop/static/img/';
                            if ($itemImg && file_exists(ROOT_PATH . '/' . $itemImg)) {
                                $img = BASE_URL . '/' . $itemImg;
                            } else {
                                $img = BASE_URL . $shopImgPath . 'noimg.jpg';
                            } ?>
                        <tr>
                            <td data-label="<?=$this->getTrans('productImage') ?>">
                                <img src="<?=$img ?>" alt="<?=$this->escape($itemName) ?>"/>
                            </td>
                            <td data-label="<?=$this->getTrans('productName') ?>">
                                <b><?=$this->escape($itemName); ?></b><br /><small><?=$this->escape($itemNumber); ?></small>
                            </td>
                            <td data-label="<?=$this->getTrans('singlePrice') ?> (<?=$this->getTrans('withoutTax') ?>)">
                                <?=number_format($itemPriceWithoutTax, 2, '.', '') ?> <?=$this->escape($this->get('currency')) ?>
                            </td>
                            <td data-label="<?=$this->getTrans('taxShort') ?>"><?=$itemTax ?> %</td>
                            <td data-label="<?=$this->getTrans('singlePrice') ?> (<?=$this->getTrans('withTax') ?>)">
                                <?=number_format($itemPrice, 2, '.', '') ?> <?=$this->escape($this->get('currency')) ?>
                            </td>
                            <td data-label="<?=$this->getTrans('entries') ?>" class="text-end">
                                <b><?=$product['quantity'] ?></b>
                            </td>
                            <td data-label="<?=$this->getTrans('total') ?> (<?=$this->getTrans('withTax') ?>)" class="text-end">
                                <b><?=number_format($itemPrice * $product['quantity'], 2, '.', '') ?> <?=$this->escape($this->get('currency')) ?></b>
                            </td>
                        </tr>
                            <?php
                            $subtotal_price += round($itemPrice * $product['quantity'], 2);
                        }
                        ?>
                    </tbody>
                </table>

                <?php if (!empty($_SESSION['shopping_cart'])) : ?>
                <table class="sum">
                    <tr>
                        <th>
                            <?=$this->getTrans('deliveryCosts') ?>
                        </th>
                        <td data-label="<?=$this->getTrans('deliveryCosts') ?>" class="text-end">
                            <?php $shipping_costs = ($willCollectAllowedAndChecked) ? 0 : max($arrayShippingCosts); ?>
                            <?=number_format($shipping_costs, 2, '.', '') ?> <?=$this->escape($this->get('currency')) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?=$this->getTrans('subtotal') ?> (<?=$this->getTrans('withTax') ?>)
                        </th>
                        <td data-label="<?=$this->getTrans('subtotal') ?> (<?=$this->getTrans('withTax') ?>)" class="text-end">
                            <?php $total_price = array_sum($arrayPrices) + $shipping_costs; ?>
                            <?=number_format($total_price, 2, '.', '') ?> <?=$this->escape($this->get('currency')) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?=$this->getTrans('subtotal') ?> (<?=$this->getTrans('withoutTax') ?>)
                        </th>
                        <td data-label="<?=$this->getTrans('subtotal') ?> (<?=$this->getTrans('withTax') ?>)" class="text-end">
                            <?php $sumPricewithoutTax = array_sum($arrayPricesWithoutTax) + round(($shipping_costs / (100 + max($arrayTaxes))) * 100, 2); ?>
                            <?=number_format($sumPricewithoutTax, 2, '.', '') ?> <?=$this->escape($this->get('currency')) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?=$this->getTrans('tax') ?>
                        </th>
                        <td data-label="<?=$this->getTrans('tax') ?>" class="text-end">
                            <?php $differenzTax = round($total_price - $sumPricewithoutTax, 2); ?>
                            <?=number_format($differenzTax, 2, '.', '') ?> <?=$this->escape($this->get('currency')) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <b><?=$this->getTrans('totalPrice') ?></b>
                        </th>
                        <td data-label="<?=$this->getTrans('totalPrice') ?>" class="text-end">
                            <b><?=number_format($total_price, 2, '.', '') ?> <?=$this->escape($this->get('currency')) ?></b>
                        </td>
                    </tr>
                </table>
                <?php else : ?>
                    <?php unset($_SESSION['shopping_cart']); ?>
                    <?=$this->getTrans('noProductInCart') ?>
                    <div class="row space20"></div>
                    <a href="<?=$this->getUrl('shop/index') ?>#shopAnker" class="btn btn-secondary">
                        <i class="fa-solid fa-backward"></i> <?=$this->getTrans('back') ?>
                    </a>
                <?php endif; ?>
            </div>

        <?php if (isset($_SESSION['shopping_cart'])) : ?>
            <h4><?=$this->getTrans('confirmation') ?></h4>
            <div class="row space20"></div>

            <div class="row mb-3<?=$this->validation()->hasError('acceptOrder') ? ' has-error' : '' ?>">
                <label for="acceptOrder" class="col-xl-2 col-form-label">
                    <?=$this->getTrans('acceptOrder') ?>&nbsp;*
                </label>
                <div class="col-xl-9" style="position:relative;">
                    <input type="checkbox"
                           class="acceptcheckbox"
                           id="acceptOrder"
                           name="acceptOrder"
                           value="1"
                        <?=($this->originalInput('acceptOrder') != '' ? 'checked' : '') ?> />
                </div>
            </div>

            <div class="row space20"></div>
            <?=$this->getTrans('acceptText') ?>

            <div class="row space20"></div>

            <div class="col-xl-12 text-center">
                <div class="btn-group btn-group-sm">
                    <a class="btn btn-outline-secondary" href="<?=$this->getUrl('shop/index') ?>#shopAnker">
                        <i class="fa-solid fa-backward"></i> <?=$this->getTrans('back') ?>
                    </a>
                    <a class="btn btn-outline-secondary" href="<?=$this->getUrl('shop/index/cart') ?>#shopAnker">
                        <i class="fa-solid fa-shopping-basket"></i> <?=$this->getTrans('menuCart') ?>
                    </a>
                    <a class="btn btn-outline-secondary" href="<?=$this->getUrl('shop/index/agb') ?>#shopAnker" target="_blank">
                        <i class="fa-solid fa-gavel"></i> <?=$this->getTrans('menuAGB') ?>
                    </a>
                </div>
                <br />
                <button type="submit" class="btn btn-warning mt1" name="saveOrder" value="save">
                    <?=$this->getTrans('completePurchase') ?> <i class="fa-solid fa-forward"></i>
                </button>
            </div>
        <?php endif; ?>
        </form>

    <?php } else { ?>
        <?=$this->getTrans('infoLogin') ?><br />
        <div class="row space20"></div>
        <form action="<?=$this->getUrl(['module' => 'user', 'controller' => 'login', 'action' => 'index']) ?>" method="post">
            <input type="hidden" name="login_redirect_url" value="<?=$this->getUrl(['module' => 'shop', 'controller' => 'index', 'action' => 'order']) ?>" />
            <?php
            echo $this->getTokenField();
            $errors = $this->get('errors');
            ?>
            <div class="row mb-3">
                <div class="col-xl-8">
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-user"></i></span>
                        <input type="text"
                               class="form-control"
                               name="login_emailname"
                               placeholder="<?=$this->getTrans('nameEmail') ?>" />
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-xl-8">
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-lock"></i></span>
                        <input type="password"
                               class="form-control"
                               name="login_password"
                               placeholder="<?=$this->getTrans('password') ?>" />
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-xl-8">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="rememberMe" value="rememberMe"> <?=$this->getTrans('rememberMe') ?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-xl-12">
                    <button type="submit" class="btn btn-outline-secondary" name="login">
                        <i class="fa-solid fa-right-to-bracket"></i> <?=$this->getTrans('login') ?>
                    </button>
                </div>
            </div>
        </form>
        <?php if ($this->get('regist_accept') == '1'): ?>
            <a href="<?=$this->getUrl(['module' => 'user', 'controller' => 'regist', 'action' => 'index']) ?>"><?=$this->getTrans('register') ?></a><br />
        <?php endif; ?>
        <a href="<?=$this->getUrl(['module' => 'user', 'controller' => 'login', 'action' => 'forgotpassword']) ?>"><?=$this->getTrans('forgotPassword') ?></a>
    <?php } ?>
    <?php
} else { ?>
    <?=$this->getTrans('noProductInCart') ?>
    <div class="row space20"></div>
    <a href="<?=$this->getUrl('shop/index') ?>#shopAnker" class="btn btn-secondary">
        <i class="fa-solid fa-backward"></i> <?=$this->getTrans('back') ?>
    </a>
<?php } ?>

<script>
    $(document).ready(function() {
        let differentInvoiceAddress = $('#differentInvoiceAddress');
        let dropdownDeliveryAddress = $('#dropdownDeliveryAddress');
        let dropdownInvoiceAddress = $('#dropdownInvoiceAddress');

        //set initial state.
        if(differentInvoiceAddress.is(':checked')) {
            $('#invoiceAddress').show();  // checked
        } else {
            $('#invoiceAddress').hide();  // unchecked
        }

        differentInvoiceAddress.change(function() {
            $('#invoiceAddress').toggle();
            differentInvoiceAddress.val(this.checked);
        });

        dropdownDeliveryAddress.change(function () {
            if (dropdownDeliveryAddress.val() !== '') {
                $('#deliveryAddress :input').prop('readonly', true);
                dropdownDeliveryAddress.prop('readonly', false);
            } else {
                $('#deliveryAddress :input').prop('readonly', false);
            }
        });

        dropdownInvoiceAddress.change(function () {
            if (dropdownInvoiceAddress.val() !== '') {
                $('#invoiceAddress :input').prop('readonly', true);
                dropdownInvoiceAddress.prop('readonly', false);
            } else {
                $('#invoiceAddress :input').prop('readonly', false);
            }
        });
    });
</script>
