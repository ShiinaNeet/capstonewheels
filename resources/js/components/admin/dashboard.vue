<template>
    <div class="mx-5 mb-2" id="dashboard-wrapper">
        <div>
            <va-tabs
            v-model="tab"
            class="mx-0 px-0 pb-0"
            grow
            >
                <template #tabs>
                    <va-tab
                    v-for="tab in tabs"
                    :key="tab"
                    >
                        {{ tab }}
                    </va-tab>
                </template>
            </va-tabs>

            <va-card>
                <va-card-content class="p-[5px!important]">
                    <div
                    v-if="
                        tabs[tab] === 'Enrollment' &&
                        this.$root.auth.userType !== 1
                    ">
                        <enrollment_tb />
                    </div>
                    <div
                    v-if="
                        tabs[tab] === 'Schedule'
                    ">
                        <schedule_tb />
                    </div>
                    <div
                    v-if="
                        tabs[tab] === 'Payment' &&
                        this.$root.auth.userType !== 1
                    ">
                        <payment_tb />
                    </div>
                    <div
                    v-if="
                        tabs[tab] === 'Inquiry' &&
                        this.$root.auth.userType !== 0 &&
                        this.$root.auth.userType !== 1
                    ">
                        <inquiry_tb />
                    </div>
                </va-card-content>
            </va-card>
        </div>
        <div class="mx-0 mt-[7px] mb-5">
            <div class="va-title text-neutral-400">
                Input fields with
                <span class="text-sm leading-[0.25rem]">*</span>
                are required
            </div>
        </div>
    </div>
</template>

<script>
import enrollment_tbl from '@/components/dashboard/tabs/enrollment.vue';
import schedule_tbl from '@/components/dashboard/tabs/schedule.vue';
import payment_tbl from '@/components/dashboard/tabs/payment.vue';
import inquiry_tbl from '@/components/dashboard/tabs/inquirys.vue';

export default {
    data () {
        return {
            tabs: ['Enrollment', 'Schedule', 'Payment', 'Inquiry'],
            tab: 0,
        }
    },
    components: {
        enrollment_tb: enrollment_tbl,
        schedule_tb: schedule_tbl,
        payment_tb: payment_tbl,
        inquiry_tb: inquiry_tbl,
    },
    beforeMount () {
        this.tabs = this.tabs.filter(item => {
            return this.$root.auth.userType === 0 ? (item !== 'Enrollment' && item !== 'Inquiry') : item;
        });

        this.tabs = this.tabs.filter(item => {
            return this.$root.auth.userType === 1 ? (item !== 'Enrollment' && item !== 'Payment' && item !== 'Inquiry') : item;
        });
    },
}
</script>
