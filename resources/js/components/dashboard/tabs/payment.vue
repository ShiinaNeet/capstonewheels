<template>
    <div id="hmenu">
        <div class="flex">
            <div
            id="t-search"
            class="shrink mr-2"
            >
                <div class="max-w-[210px!important]">
                    <va-input
                    v-model="filter"
                    placeholder="Search..."
                    >
                        <template #appendInner>
                            <va-icon name="search" color="#C2C2C2" size="20px" />
                        </template>
                    </va-input>
                </div>
            </div>
            <div>
                <va-tabs
                v-model="stab"
                @update:modelValue="
                    filtered = null,
                    filter = ''
                ">
                    <template #tabs>
                        <va-tab
                        v-for="stab in stabs"
                        :key="stab"
                        >
                            <va-icon
                            :name="stab.icon"
                            class="mr-2"
                            size="small"
                            />
                                <div class="va-title">
                                    {{ stab.title }}
                                </div>
                        </va-tab>
                    </template>
                </va-tabs>
            </div>
        </div>

        <va-divider class="m-[0!important]" />

        <va-data-table
        id="data-table"
        v-if="stabs[stab].title === 'History'"
        :items="payment.history.items"
        :columns="payment.history.table.columns"
        :per-page="payment.history.table.perPage"
        :current-page="payment.history.table.currPage"
        no-data-html="No payment(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        >
            <template #cell(reference_no)="{ value }">
                {{ value }}
            </template>
            <template #cell(name)="{ rowData }">
                {{ rowData.firstname + ' ' + rowData.lastname }}
            </template>
            <template #cell(amount)="{ rowData }">
                ₱ {{ parseFloat(getServiceTotalAmount(rowData.services)).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
            </template>
            <template #cell(mode_of_payment)="{ value }">
                <va-badge
                :text="value && (mop[value].label)"
                :color="value && (mop[value].color)"
                />
            </template>
            <template #cell(created_at)="{ value }">
                {{ formatDate(value, 'MMM. Do YYYY', 'Invalid Date') }}
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(payment.history.items)">
                    <td
                    id="pagination"
                    :colspan="payment.history.table.columns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="payment.history.table.currPage"
                            :pages="filter == '' ? $root.tblPagination(payment.history.items) : (pages, payment.history.table.currPage = 1)"
                            input
                            />
                        </div>
                    </td>
                </tr>
            </template>
        </va-data-table>
    </div>
</template>

<script>
import formatDate from '@/functions/formatdate.js';

export default {
    data () {
        const paym = {
            tblColumns: [
                { key: "reference_no", label: "Reference No.", width: 150, sortable: false },
                { key: "name", label: "Name", width: 200, sortable: true },
                { key: "amount", label: "Amount", width: 110, sortable: false },
                { key: "mode_of_payment", label: "MOP", width: 85, sortable: true },
                { key: "created_at", label: "Created On", width: 125, sortable: true },
            ],
        };

        return {
            payment: {
                history: {
                    table: {
                        columns: paym.tblColumns.filter(item => {
                            return this.$root.auth.userType === 0 ? (item.key !== "name") : item;
                        }),
                        perPage: this.$root.config.tblPerPage,
                        currPage: this.$root.config.tblCurrPage,
                    },
                    items: [],
                    activePreviewRow: null,
                },
            },
            filtered: null,
            filter: "",
            mop: [
                { label: 'Cash', color: 'secondary', value: 0 },
                { label: 'Paymongo', color: 'success', value: 1 },
            ],
            stabs: [
                { title: 'History', icon: 'history' },
            ],
            stab: 0,
        }
    },
    computed: {
        pages() {
            return this.$root.config.tblPerPage && this.$root.config.tblPerPage !== 0
            ? Math.ceil(this.filtered.length / this.$root.config.tblPerPage)
            : this.filtered.length;
        },
    },
    mounted () {
        this.getEnrollments();
    },
    methods: {
        getDateTimeSchedule(item) {
            return this.formatDate(item.day_of_week + ' ' + item.time_start, 'MMM. Do YYYY (hh:mma')
                + '-' + this.formatDate(item.day_of_week + ' ' + item.time_end, 'hh:mma)');
        },
        getServiceTotalAmount(items) {
            let total = 0;
            for (let item of items) {
                total += item.price;
            }
            return total;
        },
        getEnrollments() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: this.$root.auth.userType === 0 ? '/student/payments' : '/get_enrollments',
            }).then(response => {
                if (response.data.status == 1) {
                    let result;
                    if (this.$root.auth.userType !== 0 && response.data.result.verified)
                        result = response.data.result.verified;
                    else
                        result = response.data.result;

                    this.payment.history.items = result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    },
}
</script>
