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
        v-if="stabs[stab].title === 'Pending'"
        :items="stud.pending.items"
        :columns="stud.pending.table.columns"
        :per-page="stud.pending.table.perPage"
        :current-page="stud.pending.table.currPage"
        no-data-html="No enrollment(s) to show"
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
            <template #cell(services)="{ row, isExpanded }">
                <va-button
                class="w-full"
                preset="secondary"
                size="small"
                :icon="isExpanded ? 'va-arrow-up': 'va-arrow-down'"
                @click="row.toggleRowDetails(), stud.pending.activePreviewRow = row"
                >
                    {{ !isExpanded ? 'Show': 'Hide' }}
                </va-button>
            </template>
            <template #expandableRow="{ rowData }">
                <va-tree-view
                :nodes="rowData.services"
                text-by="service_name"
                children-by="schedules"
                expand-all
                >
                    <template #content="node">
                        <div
                        v-if="node.service_name"
                        class="flex items-center gap-x-[7px]"
                        >
                            <div class="shrink">
                                <va-avatar
                                :src="$root.forgeImageFile(node.image, 'services', false)"
                                :fallback-src="$root.forgeImageFile(null, null, false)"
                                color="#F5F5F5"
                                size="small"
                                square
                                />
                            </div>
                            <div class="shrink">
                                <div class="font-semibold mb-1">
                                    {{ node.service_name }}
                                </div>
                                <div class="flex gap-x-[15px]">
                                    <div class="shrink">
                                        Batch:
                                        <span class="ml-2 va-text-secondary">
                                            {{ node.batch }}
                                        </span>
                                    </div>
                                    <div class="shrink">
                                        Price:
                                        <span class="ml-2 va-text-secondary">
                                            ₱ {{ parseFloat(node.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                        </span>
                                    </div>
                                    <div
                                    v-if="node.type == 0"
                                    class="shrink"
                                    >
                                        Room:
                                        <span class="ml-2 va-text-secondary">
                                            {{ node.room }}
                                        </span>
                                    </div>
                                    <div
                                    v-if="node.type == 1"
                                    class="shrink"
                                    >
                                        Vehicle:
                                        <span class="ml-2 va-text-secondary">
                                            {{ node.vehicle }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                        v-if="node.day_of_week"
                        >
                            {{ getDateTimeSchedule(node) }}
                        </div>
                    </template>
                </va-tree-view>
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
            <template #cell(id)="{ rowData }">
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Add Payment"
                preset="plain"
                icon="add_card"
                @click="
                    stud.pending.modal.data.payment_id = rowData.id,
                    stud.pending.modal.amount.display = parseFloat(getServiceTotalAmount(rowData.services)).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                    stud.pending.modal.window = !stud.pending.modal.window
                "
                />
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(stud.pending.items)">
                    <td
                    id="pagination"
                    :colspan="stud.pending.table.columns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="stud.pending.table.currPage"
                            :pages="filter == '' ? $root.tblPagination(stud.pending.items) : (pages, stud.pending.table.currPage = 1)"
                            input
                            />
                        </div>
                    </td>
                </tr>
            </template>
        </va-data-table>

        <va-data-table
        id="data-table"
        v-if="stabs[stab].title === 'Enrolled'"
        :items="stud.approved.items"
        :columns="stud.approved.table.columns"
        :per-page="stud.approved.table.perPage"
        :current-page="stud.approved.table.currPage"
        no-data-html="No enrollment(s) to show"
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
            <template #cell(services)="{ row, isExpanded }">
                <va-button
                class="w-full"
                preset="secondary"
                size="small"
                :icon="isExpanded ? 'va-arrow-up': 'va-arrow-down'"
                @click="row.toggleRowDetails(), stud.approved.activePreviewRow = row"
                >
                    {{ !isExpanded ? 'Show': 'Hide' }}
                </va-button>
            </template>
            <template #expandableRow="{ rowData }">
                <va-tree-view
                :nodes="rowData.services"
                text-by="service_name"
                children-by="schedules"
                expand-all
                >
                    <template #content="node">
                        <div
                        v-if="node.service_name"
                        class="flex items-center gap-x-[7px]"
                        >
                            <div class="shrink">
                                <va-avatar
                                v-if="node.image"
                                :src="$root.forgeImageFile(node.image, 'services', false)"
                                :fallback-src="$root.forgeImageFile(null, null, false)"
                                color="#F5F5F5"
                                size="small"
                                square
                                />
                            </div>
                            <div class="shrink">
                                <div class="font-semibold mb-1">
                                    {{ node.service_name }}
                                </div>
                                <div class="flex gap-x-[15px]">
                                    <div class="shrink">
                                        Batch:
                                        <span class="ml-2 va-text-secondary">
                                            {{ node.batch }}
                                        </span>
                                    </div>
                                    <div class="shrink">
                                        Price:
                                        <span class="ml-2 va-text-secondary">
                                            ₱ {{ parseFloat(node.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                        </span>
                                    </div>
                                    <div
                                    v-if="node.type == 0"
                                    class="shrink"
                                    >
                                        Room:
                                        <span class="ml-2 va-text-secondary">
                                            {{ node.room }}
                                        </span>
                                    </div>
                                    <div
                                    v-if="node.type == 1"
                                    class="shrink"
                                    >
                                        Vehicle:
                                        <span class="ml-2 va-text-secondary">
                                            {{ node.vehicle }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                        v-if="node.day_of_week"
                        >
                            {{ getDateTimeSchedule(node) }}
                        </div>
                    </template>
                </va-tree-view>
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
                <tr v-if="$root.tblPagination(stud.approved.items)">
                    <td
                    id="pagination"
                    :colspan="stud.approved.table.columns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="stud.approved.table.currPage"
                            :pages="filter == '' ? $root.tblPagination(stud.approved.items) : (pages, stud.approved.table.currPage = 1)"
                            input
                            />
                        </div>
                    </td>
                </tr>
            </template>
        </va-data-table>

        <va-data-table
        id="data-table"
        v-if="stabs[stab].title === 'Cancelled'"
        :items="stud.cancelled.items"
        :columns="stud.cancelled.table.columns"
        :per-page="stud.cancelled.table.perPage"
        :current-page="stud.cancelled.table.currPage"
        no-data-html="No cancelled enrollment(s) to show"
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
            <template #cell(services)="{ row, isExpanded }">
                <va-button
                class="w-full"
                preset="secondary"
                size="small"
                :icon="isExpanded ? 'va-arrow-up': 'va-arrow-down'"
                @click="row.toggleRowDetails(), stud.approved.activePreviewRow = row"
                >
                    {{ !isExpanded ? 'Show': 'Hide' }}
                </va-button>
            </template>
            <template #expandableRow="{ rowData }">
                <va-tree-view
                :nodes="rowData.services"
                text-by="service_name"
                children-by="schedules"
                expand-all
                >
                    <template #content="node">
                        <div
                        v-if="node.service_name"
                        class="flex items-center gap-x-[7px]"
                        >
                            <div class="shrink">
                                <va-avatar
                                v-if="node.image"
                                :src="$root.forgeImageFile(node.image, 'services', false)"
                                :fallback-src="$root.forgeImageFile(null, null, false)"
                                color="#F5F5F5"
                                size="small"
                                square
                                />
                            </div>
                            <div class="shrink">
                                <div class="font-semibold mb-1">
                                    {{ node.service_name }}
                                </div>
                                <div class="flex gap-x-[15px]">
                                    <div class="shrink">
                                        Batch:
                                        <span class="ml-2 va-text-secondary">
                                            {{ node.batch }}
                                        </span>
                                    </div>
                                    <div class="shrink">
                                        Price:
                                        <span class="ml-2 va-text-secondary">
                                            ₱ {{ parseFloat(node.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                        </span>
                                    </div>
                                    <div
                                    v-if="node.type == 0"
                                    class="shrink"
                                    >
                                        Room:
                                        <span class="ml-2 va-text-secondary">
                                            {{ node.room }}
                                        </span>
                                    </div>
                                    <div
                                    v-if="node.type == 1"
                                    class="shrink"
                                    >
                                        Vehicle:
                                        <span class="ml-2 va-text-secondary">
                                            {{ node.vehicle }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                        v-if="node.day_of_week"
                        >
                            {{ getDateTimeSchedule(node) }}
                        </div>
                    </template>
                </va-tree-view>
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
                <tr v-if="$root.tblPagination(stud.cancelled.items)">
                    <td
                    id="pagination"
                    :colspan="stud.cancelled.table.columns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="stud.cancelled.table.currPage"
                            :pages="filter == '' ? $root.tblPagination(stud.cancelled.items) : (pages, stud.cancelled.table.currPage = 1)"
                            input
                            />
                        </div>
                    </td>
                </tr>
            </template>
        </va-data-table>

        <va-data-table
        id="data-table"
        v-if="stabs[stab].title === 'Completed'"
        :items="stud.completed.items"
        :columns="stud.completed.table.columns"
        :per-page="stud.completed.table.perPage"
        :current-page="stud.completed.table.currPage"
        no-data-html="No completed enrollment(s) with completed schedule to show"
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
            <template #cell(services)="{ row, isExpanded }">
                <va-button
                class="w-full"
                preset="secondary"
                size="small"
                :icon="isExpanded ? 'va-arrow-up': 'va-arrow-down'"
                @click="row.toggleRowDetails(), stud.approved.activePreviewRow = row"
                >
                    {{ !isExpanded ? 'Show': 'Hide' }}
                </va-button>
            </template>
            <template #expandableRow="{ rowData }">
                <va-tree-view
                :nodes="rowData.services"
                text-by="service_name"
                children-by="schedules"
                expand-all
                >
                    <template #content="node">
                        <div
                        v-if="node.service_name"
                        class="flex items-center gap-x-[7px]"
                        >
                            <div class="shrink">
                                <va-avatar
                                v-if="node.image"
                                :src="$root.forgeImageFile(node.image, 'services', false)"
                                :fallback-src="$root.forgeImageFile(null, null, false)"
                                color="#F5F5F5"
                                size="small"
                                square
                                />
                            </div>
                            <div class="shrink">
                                <div class="font-semibold mb-1">
                                    {{ node.service_name }}
                                </div>
                                <div class="flex gap-x-[15px]">
                                    <div class="shrink">
                                        Batch:
                                        <span class="ml-2 va-text-secondary">
                                            {{ node.batch }}
                                        </span>
                                    </div>
                                    <div class="shrink">
                                        Price:
                                        <span class="ml-2 va-text-secondary">
                                            ₱ {{ parseFloat(node.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                        </span>
                                    </div>
                                    <div
                                    v-if="node.type == 0"
                                    class="shrink"
                                    >
                                        Room:
                                        <span class="ml-2 va-text-secondary">
                                            {{ node.room }}
                                        </span>
                                    </div>
                                    <div
                                    v-if="node.type == 1"
                                    class="shrink"
                                    >
                                        Vehicle:
                                        <span class="ml-2 va-text-secondary">
                                            {{ node.vehicle }}
                                        </span>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        <div
                        v-if="node.day_of_week"
                        >
                            {{ getDateTimeSchedule(node) }}
                        </div>
                    </template>
                </va-tree-view>
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
                <tr v-if="$root.tblPagination(stud.completed.items)">
                    <td
                    id="pagination"
                    :colspan="stud.completed.table.columns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="stud.completed.table.currPage"
                            :pages="filter == '' ? $root.tblPagination(stud.completed.items) : (pages, stud.completed.table.currPage = 1)"
                            input
                            />
                        </div>
                    </td>
                </tr>
            </template>
        </va-data-table>

    </div>

    <va-modal
    v-model.lazy="stud.pending.modal.window"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Add Payment
                </div>
                <va-input
                v-model="stud.pending.modal.amount.display"
                label="Amount"
                requiredMark
                class="w-full mb-2"
                maxlength="10"
                mask="numeral"
                disabled
                >
                    <template #appendInner>
                        <span>₱</span>
                    </template>
                </va-input>
                <va-input
                v-model="stud.pending.modal.data.amount_paid"
                label="Confirm Amount"
                requiredMark
                class="w-full mb-2"
                maxlength="10"
                mask="numeral"
                :rules="[(v) => v && v.length > 0 || 'Amount is required.']"
                :error="stud.pending.modal.amount.empty"
                :error-messages="stud.pending.modal.amount.err === 1 ? 'Amount must be exact.' : 'Amount is required.'"
                @keyup="
                    stud.pending.modal.amount.empty = false,
                    stud.pending.modal.amount.err = 0
                "
                >
                    <template #appendInner>
                        <span>₱</span>
                    </template>
                </va-input>
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            stud.pending.modal.data = { ...stud.pending.modal.resetData },
                            stud.pending.modal.amount.empty = false,
                            stud.pending.modal.saved = false,
                            stud.pending.modal.window = !stud.pending.modal.window
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="stud.pending.modal.saved"
                        :disabled="stud.pending.modal.saved"
                        @click="stud.pending.modal.saved = true, confirmPaymentRecord()"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>
</template>

<script>
import formatDate from '@/functions/formatdate.js';

const newPaym = {
    payment_id: null,
    amount_to_pay: null,
    amount_paid: null,
}

export default {
    data () {
        const enrollment = {
            tblColumns: [
                { key: "reference_no", label: "Reference No.", width: 150, sortable: false },
                { key: "name", label: "Name", width: 200, sortable: true },
                { key: "services", label: "Services", width: 80, tdAlign: "center", sortable: false },
                { key: "amount", label: "Amount", width: 110, sortable: false },
                { key: "mode_of_payment", label: "MOP", width: 85, sortable: true },
                { key: "created_at", label: "Created On", width: 125, sortable: true },
                { key: "id", label: "Action", width: 60, sortable: false },
            ],
        };

        const completeEnrollment = {
            tblColumns: [
                { key: "reference_no", label: "Reference No.", width: 150, sortable: false },
                { key: "name", label: "Name", width: 200, sortable: true },
                { key: "services", label: "Services", width: 80, tdAlign: "center", sortable: false },
                { key: "amount", label: "Amount", width: 110, sortable: false },
                { key: "mode_of_payment", label: "MOP", width: 85, sortable: true },
                { key: "created_at", label: "Created On", width: 125, sortable: true },
                { key: "id", label: "Action", width: 60, sortable: false },
            ],
        };
        return {
            stud: {
                pending: {
                    table: {
                        columns: enrollment.tblColumns,
                        perPage: this.$root.config.tblPerPage,
                        currPage: this.$root.config.tblCurrPage,
                    },
                    modal: {
                        window: false,
                        amount: {
                            display: null,
                            err: 0,
                            empty: false,
                        },
                        saved: false,
                        resetData: { ...newPaym },
                        data: { ...newPaym },
                    },
                    items: [],
                    activePreviewRow: null
                },
                approved: {
                    table: {
                        columns: enrollment.tblColumns.slice(0,-1),
                        perPage: this.$root.config.tblPerPage,
                        currPage: this.$root.config.tblCurrPage,
                    },
                    items: [],
                    activePreviewRow: null
                },
                cancelled: {
                    table: {
                        columns: enrollment.tblColumns.slice(0,-1),
                        perPage: this.$root.config.tblPerPage,
                        currPage: this.$root.config.tblCurrPage,
                    },
                    items: [],
                    activePreviewRow: null
                },
                completed: {
                    table: {
                        columns: completeEnrollment.tblColumns.slice(0,-1),
                        perPage: this.$root.config.tblPerPage,
                        currPage: this.$root.config.tblCurrPage,
                    },
                    items: [],
                    activePreviewRow: null
                },
            },
            filtered: null,
            filter: "",
            mop: [
                { label: 'Cash', color: 'secondary', value: 0 },
                { label: 'Paymongo', color: 'success', value: 1 },
            ],
            stabs: [
                { title: 'Pending', icon: 'edit_calendar' },
                { title: 'Enrolled', icon: 'verified' },
                { title: 'Completed', icon: 'done' },
                { title: 'Cancelled', icon: 'cancel' },
               
                // { title: 'Failed', icon: 'tab_unselected' },
                // { title: 'History', icon: 'recent_actors' },
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
            return total.toString();
        },
        confirmPaymentRecord() {
            if (this.stud.pending.modal.amount.display === parseFloat(this.stud.pending.modal.data.amount_paid).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })) {
                this.stud.pending.modal.data.amount_to_pay = parseFloat(this.stud.pending.modal.amount.display.replace(/,/g,'')).toString();

                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/payment/update',
                    data: this.stud.pending.modal.data
                }).then(response => {
                    this.$root.prompt(response.data.text);
                    if (response.data.status == 1) {
                        this.stud.pending.modal.data = { ...newPaym };
                        this.stud.pending.modal.amount.empty = false;
                        this.stud.pending.modal.saved = false;
                        this.stud.pending.modal.window = !this.stud.pending.modal.window;

                        this.getEnrollments();

                        this.stud.pending.activePreviewRow && this.stud.pending.activePreviewRow.toggleRowDetails(false);
                        this.stud.pending.activePreviewRow = null;
                    }
                }).catch(error => {
                    this.$root.prompt(error.response.data.message);
                });
            } else {
                if (this.stud.pending.modal.data.amount_paid)
                    this.stud.pending.modal.amount.err = 1;
                this.stud.pending.modal.amount.empty = true;
                this.stud.pending.modal.saved = false;
            }
        },
        getEnrollments() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/get_enrollments',
            }).then(response => {
                if (response.data.status == 1) {
                    this.stud.pending.items = response.data.result.pending;
                    this.stud.approved.items = response.data.result.verified;
                    this.stud.cancelled.items = response.data.result.cancelled_enrollments;
                    this.stud.completed.items = response.data.result.completed_enrollments;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    },
}
</script>
