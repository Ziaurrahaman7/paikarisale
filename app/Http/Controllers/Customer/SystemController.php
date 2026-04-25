<?php

namespace App\Http\Controllers\Customer;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use App\Models\ShippingMethod;
use App\Models\CartShipping;
use App\Utils\CartManager;
use App\Utils\OrderManager;
use App\Utils\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function setPaymentMethod($name): JsonResponse
    {
        if (auth('customer')->check() || session()->has('mobile_app_payment_customer_id')) {
            session()->put('payment_method', $name);
            return response()->json(['status' => 1]);
        }
        return response()->json(['status' => 0]);
    }

    public function setShippingMethod(Request $request): JsonResponse
    {
        if ($request['cart_group_id'] == 'all_cart_group') {
            foreach (CartManager::get_cart_group_ids() as $groupId) {
                $request['cart_group_id'] = $groupId;
                self::insertIntoCartShipping($request);
            }
        } else {
            self::insertIntoCartShipping($request);
        }
        return response()->json(['status' => 1]);
    }

    public static function insertIntoCartShipping($request): void
    {
        $shipping = CartShipping::where(['cart_group_id' => $request['cart_group_id']])->first();
        if (!$shipping) {
            $shipping = new CartShipping();
        }

        $method = ShippingMethod::find($request['id']);
        $shipping['cart_group_id'] = $request['cart_group_id'];
        $shipping['shipping_method_id'] = $method->id ?? null;
        $shipping['shipping_cost'] = $method->cost ?? 0;
        $shipping->save();

        if (session('coupon_code') && session('coupon_discount')) {
            $result = OrderManager::getTotalCouponAmount(request: $request, couponCode: session('coupon_code'));
            if (!$result['status']) {
                session()->forget(['coupon_code', 'coupon_type', 'coupon_bearer', 'coupon_discount', 'coupon_seller_id']);
            }
        }
    }

    public function getChooseShippingAddressOther(Request $request): JsonResponse
    {
        $billingInputByCustomer = getWebConfig(name: 'billing_input_by_customer');
        $isGuestCustomer = !auth('customer')->check();

        $shipping = [];
        $billing = [];

        if (is_string($request->shipping)) {
            parse_str($request->shipping, $shipping);
        } else {
            $shipping = $request->shipping ?? [];
        }

        if (is_string($request->billing)) {
            parse_str($request->billing, $billing);
        } else {
            $billing = $request->billing ?? [];
        }

        // Validate required fields (removed division)
        $requiredFields = ['contact_person_name', 'phone', 'address'];
        foreach ($requiredFields as $field) {
            if (empty($shipping[$field])) {
                return response()->json([
                    'errors' => translate("Please_enter_all_required_fields")
                ], 403);
            }
        }

        $phoneValue = preg_replace('/[^0-9]/', '', $shipping['phone']);
        if (strlen($phoneValue) < 4) {
            return response()->json([
                'errors' => translate('The_phone_number_must_be_at_least_4_characters')
            ], 403);
        }

        $addressId = ShippingAddress::insertGetId([
            'customer_id' => auth('customer')->id() ?? session('guest_id') ?? 0,
            'is_guest' => auth('customer')->check() ? 0 : 1,
            'contact_person_name' => $shipping['contact_person_name'],
            'address' => $shipping['address'],
            'phone' => $shipping['phone'],
            
            'address_type' => $shipping['address_type'] ?? 'others',
            'country' => $shipping['country'] ?? null,
            'city' => $shipping['city'] ?? null,
            'zip' => $shipping['zip'] ?? null,
            'email' => $shipping['email'] ?? null,
            'latitude' => $shipping['latitude'] ?? 0,
            'longitude' => $shipping['longitude'] ?? 0,
            'is_billing' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $billingAddressId = $addressId;

        if ($request['billing_addresss_same_shipping'] == 'false' && $billingInputByCustomer) {
            if (!empty($billing['billing_contact_person_name']) && !empty($billing['billing_phone']) && !empty($billing['billing_address'])) {
                $billingPhoneValue = preg_replace('/[^0-9]/', '', $billing['billing_phone']);
                if (strlen($billingPhoneValue) < 4) {
                    return response()->json([
                        'errors' => translate('The_billing_phone_number_must_be_at_least_4_characters')
                    ], 403);
                }

                $billingAddressId = ShippingAddress::insertGetId([
                    'customer_id' => auth('customer')->id() ?? session('guest_id') ?? 0,
                    'is_guest' => auth('customer')->check() ? 0 : 1,
                    'contact_person_name' => $billing['billing_contact_person_name'],
                    'address' => $billing['billing_address'],
                    'phone' => $billing['billing_phone'],
                    'division_id' => null,
                    'district_id' => $billing['billing_district'] ?? null,
                    'upazila_id' => $billing['billing_upazila'] ?? null,
                    'address_type' => $billing['billing_address_type'] ?? 'others',
                    'country' => $billing['billing_country'] ?? null,
                    'city' => $billing['billing_city'] ?? null,
                    'zip' => $billing['billing_zip'] ?? null,
                    'email' => $billing['billing_contact_email'] ?? null,
                    'latitude' => $billing['billing_latitude'] ?? 0,
                    'longitude' => $billing['billing_longitude'] ?? 0,
                    'is_billing' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        session()->put('address_id', $addressId);
        session()->put('billing_address_id', $billingAddressId);

        if ($request['is_check_create_account'] && $isGuestCustomer) {
            if (empty($request['customer_password']) || empty($request['customer_confirm_password'])) {
                return response()->json([
                    'errors' => translate('The_password_or_confirm_password_can_not_be_empty')
                ], 403);
            }
            if ($request['customer_password'] !== $request['customer_confirm_password']) {
                return response()->json([
                    'errors' => translate('The_password_and_confirm_password_must_match')
                ], 403);
            }
            if (strlen($request['customer_password']) < 7) {
                return response()->json([
                    'errors' => translate('The_password_must_be_at_least_8_characters')
                ], 403);
            }

            $newCustomerAddress = [
                'name' => $shipping['contact_person_name'],
                'email' => $shipping['email'] ?? null,
                'phone' => $shipping['phone'],
                'password' => $request['customer_password'],
            ];

            if (User::where('email', $newCustomerAddress['email'])->orWhere('phone', $newCustomerAddress['phone'])->first()) {
                return response()->json(['errors' => translate('Already_registered')], 403);
            }

            $newCustomerRegister = $this->getRegisterNewCustomer($request, $newCustomerAddress);
            session()->put('newCustomerRegister', $newCustomerRegister);
        } else {
            session()->forget(['newCustomerRegister', 'newRegisterCustomerInfo']);
        }

        return response()->json(['status' => 1], 200);
    }

    private function getRegisterNewCustomer($request, $address): array
    {
        return [
            'name' => $address['name'],
            'f_name' => $address['name'],
            'l_name' => '',
            'email' => $address['email'],
            'phone' => $address['phone'],
            'is_active' => 1,
            'password' => $address['password'],
            'referral_code' => Helpers::generate_referer_code(),
            'shipping_id' => session('address_id'),
            'billing_id' => session('billing_address_id'),
        ];
    }
}
