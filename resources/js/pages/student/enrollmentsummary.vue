<template>
    <div class="h-screen">
        <navigation />

        <div class="flex items-start">
            <div class="flex w-full py-8" style="position: sticky; top:62px;">
                <div class="shrink mx-auto lg:w-[min(100%,85vw)] sm:w-[min(100%,105vw)]" id="enrollment-summary-wrapper">
                    <va-card>
                        <va-card-content>
                            <div class="va-title mb-3">
                                Enrollment Summary
                            </div>
                            <div class="flex w-full gap-x-3">
                                <div class="flex flex-col w-1/2 justify-between px-[15px] py-1.5 rounded border border-solid border-[#ECF0F1] bg-neutral-50">
                                    <div>
                                        <va-divider class="mb-4" orientation="left">
                                            <span class="mx-2">Student Information</span>
                                        </va-divider>
                                        <div class="flex mb-[4px] items-end">
                                            <div class="w-1/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>Firstname:</div>
                                            </div>
                                            <div class="w-3/5">
                                                <VaSkeleton
                                                v-if="!esum.data.firstname"
                                                variant="text"
                                                />
                                                <div>{{ esum.data.firstname }}</div>
                                            </div>
                                        </div>
                                        <div class="flex mb-[4px] items-end">
                                            <div class="w-1/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>Middlename:</div>
                                            </div>
                                            <div class="w-3/5">
                                                <VaSkeleton
                                                v-if="!esum.data.middlename"
                                                variant="text"
                                                />
                                                <div>{{ esum.data.middlename }}</div>
                                            </div>
                                        </div>
                                        <div class="flex mb-[4px] items-end">
                                            <div class="w-1/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>Lastname:</div>
                                            </div>
                                            <div class="w-3/5">
                                                <VaSkeleton
                                                v-if="!esum.data.lastname"
                                                variant="text"
                                                />
                                                <div>{{ esum.data.lastname }}</div>
                                            </div>
                                        </div>
                                        <div class="flex mb-[4px] items-end">
                                            <div class="w-1/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>Gender:</div>
                                            </div>
                                            <div class="w-3/5">
                                                <VaSkeleton
                                                v-if="esum.data.gender == null"
                                                variant="text"
                                                :lines="1"
                                                />
                                                <div>
                                                    {{
                                                        esum.data.gender != null && (
                                                            genders[
                                                                $root.arrayFind(
                                                                    genders, (item) => item.value === esum.data.gender
                                                                )
                                                            ].name
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex mb-[4px] items-end">
                                            <div class="w-1/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>Birthdate:</div>
                                            </div>
                                            <div class="w-3/5">
                                                <VaSkeleton
                                                v-if="!esum.data.birthdate"
                                                variant="text"
                                                />
                                                <div>{{ esum.data.birthdate && (formatDate(esum.data.birthdate, 'MMMM DD, YYYY', 'Invalid Date')) }}</div>
                                            </div>
                                        </div>

                                        <va-divider class="mt-[15px!important] mb-[15px!important]" />

                                        <div class="flex mb-[4px] items-end">
                                            <div class="w-1/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>E-mail Address:</div>
                                            </div>
                                            <div class="w-3/5">
                                                <VaSkeleton
                                                v-if="!esum.data.email"
                                                variant="text"
                                                />
                                                <div>{{ esum.data.email }}</div>
                                            </div>
                                        </div>
                                        <div class="flex mb-[4px] items-end">
                                            <div class="w-1/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>Mobile No.:</div>
                                            </div>
                                            <div class="w-3/5">
                                                <VaSkeleton
                                                v-if="!esum.data.mobile"
                                                variant="text"
                                                />
                                                <div>{{ esum.data.mobile }}</div>
                                            </div>
                                        </div>

                                        <va-divider class="mt-[15px!important] mb-[15px!important]" />

                                        <div class="flex mb-[4px] items-end">
                                            <div class="w-1/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>Address:</div>
                                            </div>
                                            <div class="w-3/5">
                                                <VaSkeleton
                                                v-if="!esum.data.address"
                                                variant="text"
                                                />
                                                <div>{{ esum.data.address }}</div>
                                            </div>
                                        </div>
                                        <div class="flex mb-[4px] items-end">
                                            <div class="w-1/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>Barangay:</div>
                                            </div>
                                            <div class="w-3/5">
                                                <VaSkeleton
                                                v-if="!esum.data.barangay"
                                                variant="text"
                                                />
                                                <div>
                                                    {{
                                                        (esum.data.barangay && !!(geo.ph.barangays && geo.ph.barangays.length)) ? (
                                                            geo.ph.barangays[
                                                                $root.arrayFind(
                                                                    geo.ph.barangays, (item) => item.brgy_code === esum.data.barangay
                                                                )
                                                            ].brgy_name
                                                        ) : ''
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex mb-[4px] items-end">
                                            <div class="w-1/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>City:</div>
                                            </div>
                                            <div class="w-3/5">
                                                <VaSkeleton
                                                v-if="!esum.data.city"
                                                variant="text"
                                                />
                                                <div>
                                                    {{
                                                        (esum.data.city && !!(geo.ph.cities && geo.ph.cities.length)) ? (
                                                            geo.ph.cities[
                                                                $root.arrayFind(
                                                                    geo.ph.cities, (item) => item.city_code === esum.data.city
                                                                )
                                                            ].city_name
                                                        ) : ''
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex mb-[4px] items-end">
                                            <div class="w-1/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>Province:</div>
                                            </div>
                                            <div class="w-3/5">
                                                <VaSkeleton
                                                v-if="!esum.data.province"
                                                variant="text"
                                                />
                                                <div>
                                                    {{
                                                        (esum.data.province && !!(geo.ph.provinces && geo.ph.provinces.length)) ? (
                                                            geo.ph.provinces[
                                                                $root.arrayFind(
                                                                    geo.ph.provinces, (item) => item.province_code === esum.data.province
                                                                )
                                                            ].province_name
                                                        ) : ''
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex mb-[56px] items-end">
                                            <div class="w-1/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>Region:</div>
                                            </div>
                                            <div class="w-3/5">
                                                <VaSkeleton
                                                v-if="!esum.data.region"
                                                variant="text"
                                                />
                                                <div>
                                                    {{
                                                        (esum.data.region && !!(geo.ph.regions && geo.ph.regions.length)) ? (
                                                            geo.ph.regions[
                                                                $root.arrayFind(
                                                                    geo.ph.regions, (item) => item.region_code === esum.data.region
                                                                )
                                                            ].region_name
                                                        ) : ''
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="cservs.length">
                                            <va-divider class="mb-4" orientation="left">
                                                <span class="mx-2">Requirements</span>
                                            </va-divider>
                                            <div class="flex mb-[4px] items-end">
                                                <div class="w-full mb-[1px]">
                                                    <va-file-upload
                                                    v-model="esum.data.file"
                                                    class="my-0"
                                                    file-types=".jpg,.jpeg,.png,image/jpeg,image/png,application/pdf"
                                                    dropzone
                                                    dropZoneText="Drop file or click here to upload"
                                                    :class="!esum.fileOverSizeLimit ? 'valid' : 'error'"
                                                    :disabled="esum.saved"
                                                    @update:modelValue="esum.data.file.length > 1 && esum.data.file.shift(), esum.fileOverSizeLimit = false"
                                                    />
                                                    <div v-if="esum.fileOverSizeLimit">
                                                        <div class="va-message-list mt-[2px!important]" style="color: #E42222;">
                                                            <div class="va-message-list__message">
                                                                The file exceeds the maximum file size.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col min-w-[40%!important] max-w-[40%!important]">
                                    <div class="px-[15px] py-1.5 shrink rounded border border-solid border-[#ECF0F1] bg-neutral-50">
                                        <div>
                                            <va-divider class="mb-4" orientation="left">
                                                <span class="mx-2">Service</span>
                                            </va-divider>
                                            <va-scroll-container
                                            class="max-h-[175px!important]"
                                            color="primary"
                                            vertical
                                            >
                                                <VaSkeleton
                                                v-if="!cservs.length"
                                                class="mb-[29px] w-full"
                                                variant="squared"
                                                height="4.8rem"
                                                />
                                                <div
                                                v-else
                                                v-for="(enrollment, i) in cservs"
                                                :key="i"
                                                class="shrink w-full"
                                                :class="(i === cservs.length - 1) && ('mb-[29px]')"
                                                >
                                                    <div class="font-medium mb-2.5 text-[20px]">
                                                        <VaSkeleton
                                                        v-if="!enrollment.service.name"
                                                        variant="text"
                                                        />
                                                        {{ enrollment.service.name }}
                                                    </div>
                                                    <div class="grid gap-y-[3px]">
                                                        <div class="flex gap-x-[15px] text-[15px]">
                                                            <div class="shrink">
                                                                <p v-for="(enrol, ii) in enrollment.schedules" :key="ii">
                                                                    {{ getDateTimeSchedule(enrol) }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="flex gap-x-[15px] text-[13px]">
                                                            <div class="shrink">
                                                                Instructor:
                                                                <span class="ml-2 va-text-secondary">
                                                                    {{ enrollment.instructor.name }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex mb-[10px] gap-x-[15px] text-[13px]">
                                                            <div class="shrink">
                                                                Price:
                                                                <span class="ml-2 va-text-secondary">
                                                                    ₱ {{ parseFloat(enrollment.service.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                                                </span>
                                                            </div>
                                                            <div class="shrink">
                                                                Batch:
                                                                <span class="ml-2 va-text-secondary">
                                                                    {{ enrollment.batch }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex gap-x-[15px] text-[13px]">
                                                            <div class="shrink item-center">
                                                                Requirements:
                                                            </div>
                                                        </div>
                                                        <div class="flex gap-x-[15px] text-[13px]">
                                                            <div class="shrink">
                                                                <va-badge
                                                                v-for="(req, idx) in enrollment.service.requirements"
                                                                :key="idx"
                                                                class="mr-[3px] mb-1"
                                                                color="secondary"
                                                                :text="requirements[$root.arrayFind(requirements, (item) => item.id === req)].name"
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </va-scroll-container>
                                        </div>
                                    </div>
                                    <div class="relative pt-1.5">
                                        <div class="flex mb-[4px] items-end">
                                            <div class="w-2/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>Subtotal</div>
                                            </div>
                                            <div class="w-3/5 text-right">
                                                <VaSkeleton
                                                v-if="!cservs"
                                                variant="text"
                                                />
                                                <div>
                                                    ₱ {{ parseFloat(getServiceTotalAmount(cservs)).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex mb-[10px] items-end">
                                            <div class="w-2/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>Discount (0.00%)</div>
                                            </div>
                                            <div class="w-3/5 text-right">
                                                <VaSkeleton
                                                v-if="!cservs"
                                                variant="text"
                                                />
                                                <div>
                                                    ₱ {{ parseFloat(0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex mb-[15px] items-end">
                                            <div class="w-2/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div>Misc. Fee</div>
                                            </div>
                                            <div class="w-3/5 text-right">
                                                <VaSkeleton
                                                v-if="!cservs"
                                                variant="text"
                                                />
                                                <div>
                                                    ₱ {{ parseFloat(0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex mb-[15px] items-end">
                                            <div class="w-2/5 mb-[1px] text-[13px] va-text-secondary">
                                                <div class="font-semibold">Total</div>
                                            </div>
                                            <div class="w-3/5 text-right text-[18px]">
                                                <VaSkeleton
                                                v-if="!cservs"
                                                variant="text"
                                                />
                                                <div class="font-semibold">
                                                    ₱ {{ parseFloat(getServiceTotalAmount(cservs)).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                                </div>
                                            </div>
                                        </div>

                                        <va-divider class="mt-[5px!important] mb-[5px!important]" />

                                        <div class="flex relative mt-[15px] mb-[10px]">
                                            <div class="w-2/5 mt-[1px] mb-[1px] text-[13px] va-text-secondary">
                                                <div :class="esum.mopEmpty && ('ml-[1.65em] text-[#CC0000] transition-all duration-0')">
                                                    Mode of Payment
                                                    <span class="text-[16px]">*</span>
                                                </div>
                                            </div>
                                            <div class="w-3/5 text-[15px] text-right">
                                                <va-radio
                                                v-model="esum.data.mop"
                                                :options="mop"
                                                text-by="label"
                                                value-by="value"
                                                :rules="[(v) => v !== null || ' ']"
                                                :error="esum.mopEmpty"
                                                :error-messages="' '"
                                                :disabled="esum.saved || esum.confirmation.cancelModal "
                                                @update:modelValue="esum.mopEmpty = false"
                                                />
                                            </div>
                                        </div>

                                        <va-divider class="mt-[5px!important] mb-[5px!important]" />

                                        <div>
                                            <div class="flex w-full gap-x-3 mt-[15px]">
                                                <div class="flex w-1/2 justify-between">
                                                    <va-button
                                                    preset="secondary"
                                                    @click="
                                                        esum.saved = false,
                                                        esum.confirmation.cancelModal = !esum.confirmation.cancelModal
                                                    ">
                                                        <p class="font-normal">Cancel</p>
                                                    </va-button>
                                                </div>
                                                <div class="flex w-1/2 justify-between">
                                                    <va-button
                                                    icon="check"
                                                    :loading="esum.saved && !esum.confirmation.modal"
                                                    :disabled="esum.saved || esum.confirmation.cancelModal || !cservs.length"
                                                    @click="
                                                        esum.saved = true,
                                                        checkEnrollmentForm()
                                                    ">
                                                        <p class="font-normal">Confirm</p>
                                                    </va-button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </va-card-content>
                    </va-card>
                    <div class="mx-0 mt-[7px] mb-5">
                        <div class="va-title text-neutral-400">
                            Input fields with
                            <span class="text-sm leading-[0.25rem]">*</span>
                            are required
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <va-modal
    v-model="esum.confirmation.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Payment Confirmation
                </div>
                <div class="flex items-center gap-x-[7px] px-[15px] py-1.5 rounded border border-solid border-[#ECF0F1] bg-neutral-100">
                    <div class="shrink">
                        <div class="text-[20px] mb-[5px]">Enrollment Recorded</div>
                        <div class="grid gap-y-[3px]">
                            <div class="flex gap-x-[15px] text-[15px]">
                                <div class="shrink">
                                    {{ esum.confirmation.data.mop === 0 ? 'Go to the cashier for payment' : 'Go to the payment page' }}
                                </div>
                            </div>
                            <div class="flex gap-x-[15px] text-[13px]">
                                <div class="shrink">
                                    Mode of Payment:
                                    <span class="ml-2 va-text-secondary">
                                        {{ mop[$root.arrayFind(mop, (item) => item.value === esum.confirmation.data.mop)].label }}
                                    </span>
                                </div>
                                <div class="shrink">
                                    Total Amount:
                                    <span class="ml-2 va-text-secondary">
                                        ₱ {{ parseFloat(esum.confirmation.data.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between invisible"></div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        :loading="esum.confirmation.saved"
                        :disabled="esum.confirmation.saved"
                        @click="
                            esum.confirmation.saved = true,
                            proceedToPaymentMethod()
                        ">
                            <p class="font-normal">Proceed</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="esum.confirmation.cancelModal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Discard Enrollment
                </div>
                <va-alert color="warning">
                    <template #icon>
                        <va-icon name="warning" />
                    </template>
                    This action is irreversible
                </va-alert>
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="esum.confirmation.cancelModal = !esum.confirmation.cancelModal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        :loading="esum.confirmation.saved"
                        :disabled="esum.confirmation.saved"
                        @click="
                            esum.confirmation.saved = true,
                            $root.redirectToPage('/enrollment')
                        ">
                            <p class="font-normal">Proceed</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>
</template>

<style>
#enrollment-summary-wrapper .va-radio {
    margin-right: 3em!important;
}
#enrollment-summary-wrapper .va-radio + .va-radio {
    margin-top: 0!important;
    margin-right: 0!important;
}
#enrollment-summary-wrapper .va-message-list {
    position: absolute;
    top: 0;
    left: 0;
}
</style>

<script>
import navmenu from '@/components/navbar.vue';
import { regions, provinces, cities, barangays } from 'select-philippines-address';
import formatDate from '@/functions/formatdate.js';
import 'cleave.js/dist/addons/cleave-phone.ph.js';

export default {
    data () {
        return {
            esum: {
                geo: {
                    delay: 50,
                },
                confirmation: {
                    modal: false,
                    cancelModal: false,
                    saved: false,
                    data: {},
                },
                mopEmpty: false,
                fileOverSizeLimit: false,
                fileNotFound: false,
                saved: false,
                data: {}
            },
            mop: [
                { label: 'Cash', value: 0 },
                { label: 'Digital', value: 1 },
            ],
            genders: [
                { name: "Female", value: 0 },
                { name: "Male", value: 1 },
            ],
            geo: {
                ph: {
                    regions: [],
                    provinces: [],
                    cities: [],
                    barangays: [],
                },
            },
            requirements: [],
            // services: [],
            cservs: []
        }
    },
    components: {
        navigation: navmenu
    },
    props: [
        'serviceId',
        'batch',
    ],
    created () {
        regions().then(response => {
            this.geo.ph.regions = response;
        });
    },
    beforeMount () {
        this.genders.reverse();

        this.getUserDetails();
        this.getServices();
    },
    methods: {
        getDateTimeSchedule(item) {
            return this.formatDate(item.day_of_week + ' ' + item.time_start, 'MMM. Do YYYY (hh:mma')
                + '-' + this.formatDate(item.day_of_week + ' ' + item.time_end, 'hh:mma)');
        },
        getServiceTotalAmount(items) {
            let total = 0;
            for (let item of items) {
                total += item.service.price;
            }
            return total.toString();
        },
        checkEnrollmentForm() {
            let data = { ...this.esum.data };

            if (data.mop != null) {
                data.birthdate !== null && (data.birthdate = this.formatDate(data.birthdate, 'YYYY-MM-DD'));
                data.expiration_date !== null && (data.expiration_date = this.formatDate(data.expiration_date, 'YYYY-MM-DD'));
                data.total_amount = this.getServiceTotalAmount(this.cservs);
                data.services = this.cservs.map(item => item.service_id);
                data.batch = this.batch;
                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/student/check_enrollment_form',
                    data: data
                }).then(response => {
                    if (response.data.status == 1) {
                        // this.$root.prompt(response.data.text);
                        data.mop === 1 && (this.esum.confirmation.data.payment_url = response.data.payment_url);
                        this.esum.confirmation.data.mop = data.mop === 1 ? data.mop : 0;
                        this.esum.confirmation.data.total_amount = data.total_amount;
                        this.esum.confirmation.modal = true;
                    } else this.$root.prompt(response.data.text);
                }).catch(error => {
                    this.$root.prompt(error.response.data.message);
                });
            } else {
                this.esum.mopEmpty = true;
                this.esum.saved = false;
            }
        },
        proceedToPaymentMethod() {
            this.esum.confirmation.data.mop !== 0 ? window.location.href = this.esum.confirmation.data.payment_url : window.location = '/dashboard'
        },
        getUserDetails() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/account/details'
            }).then(response => {
                if (response.data.status == 1) {
                    if (response.data.result) {
                        this.esum.data = response.data.result;
                        // setTimeout(() => {
                            this.getGeoLocation(this.esum.data);
                        // }, this.esum.geo.delay);
                        // this.cservs = [];
                    }
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        getServices() {
            this.requirements.length === 0 && (
                axios({
                    method: 'GET',
                    type: 'JSON',
                    url: '/service/options'
                }).then(response => {
                    if (response.data.status == 1) {
                        this.requirements = response.data.result.requirements;
                    } else this.$root.prompt(response.data.text);
                }).catch(error => {
                    this.$root.prompt(error.response.data.message);
                })
            );

            axios({
                method: 'POST',
                type: 'JSON',
                url: '/student/service',
                data: { service_id: this.serviceId, batch: this.batch }
            }).then(response => {
                if (response.data.status == 1) {
                    // this.services = response.data.result;
                    // this.cservs = response.data.result.filter(item => item.id === parseInt(this.cserv));
                    this.cservs = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        getGeoLocation(string) {
            string.region && this.getProvince(string.region);
            string.province && this.getCity(string.province);
            string.city && this.getBarangay(string.city);
        },
        getProvince(string) {
            provinces(string).then(response => {
                this.geo.ph.provinces = response;
            });
        },
        getCity(string) {
            cities(string).then(response => {
                this.geo.ph.cities = response;
            });
        },
        getBarangay(string) {
            barangays(string).then(response => {
                this.geo.ph.barangays = response;
            });
        },
        formatDate
    }
}
</script>
