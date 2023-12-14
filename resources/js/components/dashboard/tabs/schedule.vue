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
        v-if="stabs[stab].title === 'Enrolled' && this.$root.auth.userType !== 0"
        :items="sched.enrollee.items"
        :columns="sched.enrollee.table.columns"
        :per-page="sched.enrollee.table.perPage"
        :current-page="sched.enrollee.table.currPage"
        no-data-html="No enrollment(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        >
            <template #headerAppend>
                <tr class="table-crud__slot">
                    <th
                    v-for="key in Object.keys(sched.enrollee.create.data)"
                    :key="key"
                    class="py-1 pr-1"
                    >
                        <va-button
                        v-if="key.includes('student_id')"
                        preset="primary"
                        icon="post_add"
                        @click="sched.enrollee.create.modal = !sched.enrollee.create.modal"
                        >
                            Add
                        </va-button>
                    </th>
                </tr>
            </template>

            <template #cell(name)="{ value }">
                {{ value }}
            </template>
            <template #cell(services)="{ row, isExpanded }">
                <va-button
                class="w-full"
                preset="secondary"
                size="small"
                :icon="isExpanded ? 'va-arrow-up': 'va-arrow-down'"
                @click="row.toggleRowDetails(), sched.enrollee.activePreviewRow = row"
                >
                    {{ !isExpanded ? 'Show': 'Hide' }}
                </va-button>
            </template>
            <template #expandableRow="{ rowData }">
                <!-- <va-tree-view
                :nodes="rowData.services"
                text-by="service_name"
                expand-all
                /> -->
                <div
                class="p-2"
                id="table-row-desc"
                >
                    <va-input
                    type="textarea"
                    :model-value="rowData.services_name"
                    placeholder="No services available"
                    readonly
                    autosize
                    outline
                    />
                </div>
            </template>
            <template #cell(date_enrolled)="{ value }">
                {{ formatDate(value, 'MMM. Do YYYY', 'Invalid Date') }}
            </template>
            <template #cell(student_id)="{ rowData }">
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Edit"
                preset="plain"
                icon="edit"
                @click="sched.enrollee.edit.data = { ...rowData }, sched.enrollee.edit.modal = !sched.enrollee.edit.modal"
                />
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Reschedule"
                preset="plain"
                icon="timelapse"
                @click="
                    sched.enrollee.edit.data = { ...rowData },
                    sched.enrollee.edit.deleteMode = 1,
                    sched.enrollee.edit.deleteModal = !sched.enrollee.edit.deleteModal
                "/>
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Delete"
                preset="plain"
                icon="delete"
                @click="
                    sched.enrollee.edit.data = { ...rowData },
                    sched.enrollee.edit.deleteMode = 0,
                    sched.enrollee.edit.deleteModal = !sched.enrollee.edit.deleteModal
                "/>
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(sched.enrollee.items)">
                    <td
                    id="pagination"
                    :colspan="sched.enrollee.table.columns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="sched.enrollee.table.currPage"
                            :pages="filter == '' ? $root.tblPagination(sched.enrollee.items) : (pages, sched.enrollee.table.currPage = 1)"
                            input
                            />
                        </div>
                    </td>
                </tr>
            </template>
        </va-data-table>

        <va-data-table
        id="data-table"
        v-if="stabs[stab].title === 'Enrolled' && this.$root.auth.userType === 0"
        :items="sched.active.items"
        :columns="sched.active.table.columns"
        :per-page="sched.active.table.perPage"
        :current-page="sched.active.table.currPage"
        no-data-html="No student(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        >
            <template #cell(date_start)="{ rowData }">
                {{ formatDate(rowData.date_start + ' ' + rowData.time_start, 'MMM. Do YYYY hh:mma', 'Invalid Date') }}
            </template>
            <template #cell(date_end)="{ rowData }">
                {{ formatDate(rowData.date_end + ' ' + rowData.time_end, 'MMM. Do YYYY hh:mma', 'Invalid Date') }}
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(sched.active.items)">
                    <td
                    id="pagination"
                    :colspan="sched.active.table.columns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="sched.active.table.currPage"
                            :pages="filter == '' ? $root.tblPagination(sched.active.items) : (pages, sched.active.table.currPage = 1)"
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
        :items="sched.completed.items"
        :columns="sched.completed.table.columns"
        :per-page="sched.completed.table.perPage"
        :current-page="sched.completed.table.currPage"
        no-data-html="No record(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        >
            <template #cell(date_start)="{ rowData }">
                {{ formatDate(rowData.date_start + ' ' + rowData.time_start, 'MMM. Do YYYY hh:mma', 'Invalid Date') }}
            </template>
            <template #cell(date_end)="{ rowData }">
                {{ formatDate(rowData.date_end + ' ' + rowData.time_end, 'MMM. Do YYYY hh:mma', 'Invalid Date') }}
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(sched.completed.items)">
                    <td
                    id="pagination"
                    :colspan="sched.completed.table.columns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="sched.completed.table.currPage"
                            :pages="filter == '' ? $root.tblPagination(sched.completed.items) : (pages, sched.completed.table.currPage = 1)"
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
        :items="sched.cancelled.items"
        :columns="sched.cancelled.table.columns"
        :per-page="sched.cancelled.table.perPage"
        :current-page="sched.cancelled.table.currPage"
        no-data-html="No schedule(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        >
            <template #cell(service_name)="{ value }">
                {{ value }}
            </template>
            <template #cell(name)="{ rowData }">
                {{ rowData.firstname + ' ' + rowData.lastname }}
            </template>
            <template #cell(datetime)="{ rowData }">
                {{ getDateTimeSchedule(rowData) }}
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(sched.cancelled.items)">
                    <td
                    id="pagination"
                    :colspan="sched.cancelled.table.columns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="sched.cancelled.table.currPage"
                            :pages="filter == '' ? $root.tblPagination(sched.cancelled.items) : (pages, sched.cancelled.table.currPage = 1)"
                            input
                            />
                        </div>
                    </td>
                </tr>
            </template>
        </va-data-table>

        <va-data-table
        id="data-table"
        v-if="stabs[stab].title === 'Archive'"
        :items="sched.archived.items"
        :columns="sched.archived.table.columns"
        :per-page="sched.archived.table.perPage"
        :current-page="sched.archived.table.currPage"
        no-data-html="No student(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        >
            <template #cell(date_archived)="{ value }">
                {{ formatDate(value, 'MMM. Do YYYY', 'Invalid Date') }}
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(sched.archived.items)">
                    <td
                    id="pagination"
                    :colspan="sched.archived.table.columns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="sched.archived.table.currPage"
                            :pages="filter == '' ? $root.tblPagination(sched.archived.items) : (pages, sched.archived.table.currPage = 1)"
                            input
                            />
                        </div>
                    </td>
                </tr>
            </template>
        </va-data-table>
    </div>

    <va-data-table
    id="data-table"
    v-if="stabs[stab].title === 'Reschedule'"
    :items="sched.reschedule.items"
    :columns="sched.reschedule.table.columns"
    :per-page="sched.reschedule.table.perPage"
    :current-page="sched.reschedule.table.currPage"
    no-data-html="No student(s) to reschedule"
    animated
    >
        <template #cell(day_of_week)="{ value }">
            {{ formatDate(value, 'MMM. Do YYYY', 'Invalid Date') }}
        </template>
        <template #cell(datetime)="{ rowData }">
            {{ getDateTimeSchedule(rowData) }}
        </template>
        <template #cell(student_id)="{ rowData }">
            <va-button
            class="mb-2 mr-2 hover:opacity-[0.65!important]"
            title="Reschedule"
            preset="plain"
            icon="running_with_errors"
            @click="sched.enrollee.reschedule.data = { ...rowData }, sched.enrollee.reschedule.modal = !sched.enrollee.reschedule.modal"
            />
        </template>

        <template #bodyAppend>
                <tr v-if="$root.tblPagination(sched.cancelled.items)">
                    <td
                    id="pagination"
                    :colspan="sched.reschedule.table.columns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="sched.cancelled.table.currPage"
                            :pages="$root.tblPagination(sched.cancelled.items)"
                            input
                            />
                        </div>
                    </td>
                </tr>
            </template>
    </va-data-table>


    <va-modal
    v-model="sched.enrollee.create.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Add Enrollee
                </div>
                <va-select
                v-model="sched.enrollee.create.data.student_id"
                label="Student"
                requiredMark
                class="mb-2 w-full"
                no-options-text="No student(s) available"
                :options="sched.enrollee.items"
                text-by="name"
                value-by="student_id"
                searchable
                clearable
                clearable-icon="backspace"
                highlight-matched-text
                :error="sched.enrollee.create.enrolleeEmpty"
                :error-messages="'The student field is required.'"
                @update:modelValue="sched.enrollee.create.enrolleeEmpty = false"
                />
                <va-select
                v-model="sched.enrollee.create.data.services"
                label="Service(s)"
                requiredMark
                class="mb-2 w-full"
                noOptionsText="No service(s) available"
                :options="sched.services"
                text-by="name"
                value-by="id"
                multiple
                searchable
                clearable
                clearable-icon="backspace"
                highlight-matched-text
                :error="sched.enrollee.create.servicesEmpty"
                :error-messages="'The service(s) field is required.'"
                @update:modelValue="sched.enrollee.create.servicesEmpty = false"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            sched.enrollee.create.data = { ...sched.enrollee.create.resetData },
                            sched.enrollee.create.enrolleeEmpty = false,
                            sched.enrollee.create.servicesEmpty = false,
                            sched.enrollee.create.saved = false,
                            sched.enrollee.create.modal = !sched.enrollee.create.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="sched.enrollee.create.saved"
                        :disabled="sched.enrollee.create.saved"
                        @click="sched.enrollee.create.saved = true, insertUpdateEnrollee('create')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="sched.enrollee.edit.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Edit Enrollee
                </div>
                <va-select
                v-model="sched.enrollee.edit.data.student_id"
                label="Student"
                requiredMark
                class="mb-2 w-full"
                no-options-text="No student(s) available"
                :options="sched.enrollee.items"
                text-by="name"
                value-by="student_id"
                searchable
                clearable
                clearable-icon="backspace"
                highlight-matched-text
                :error="sched.enrollee.edit.enrolleeEmpty"
                :error-messages="'The student field is required.'"
                @update:modelValue="sched.enrollee.edit.enrolleeEmpty = false"
                />
                <va-select
                v-model="sched.enrollee.edit.data.services"
                label="Service(s)"
                requiredMark
                class="mb-2 w-full"
                noOptionsText="No service(s) available"
                :options="sched.services"
                text-by="name"
                value-by="id"
                searchable
                clearable
                clearable-icon="backspace"
                highlight-matched-text
                :error="sched.enrollee.edit.servicesEmpty"
                :error-messages="'The service(s) field is required.'"
                @update:modelValue="sched.enrollee.edit.servicesEmpty = false"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            sched.enrollee.edit.data = { ...sched.enrollee.create.resetData },
                            sched.enrollee.edit.enrolleeEmpty = false,
                            sched.enrollee.edit.servicesEmpty = false,
                            sched.enrollee.edit.saved = false,
                            sched.enrollee.edit.modal = !sched.enrollee.edit.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="sched.enrollee.edit.saved"
                        :disabled="sched.enrollee.edit.saved"
                        @click="sched.enrollee.edit.saved = true, insertUpdateEnrollee('save')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="sched.enrollee.edit.deleteModal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    {{ sched.enrollee.edit.deleteMode == 0 ? 'Delete' : 'Reschedule' }} Enrollee
                </div>
                <va-alert
                v-if="!sched.enrollee.edit.deleteMode"
                color="warning"
                >
                    <template #icon>
                        <va-icon name="warning" />
                    </template>
                    This action is irreversible
                </va-alert>
                <va-input
                type="textarea"
                :model-value="sched.enrollee.edit.data.name"
                class="w-full mb-2 force-noresize"
                readonly
                autosize
                />
                <va-select
                v-model="sched.enrollee.edit.data.services"
                label="Service(s)"
                requiredMark
                class="mb-2 w-full"
                noOptionsText="No service(s) available"
                :options="sched.services.filter(item => sched.enrollee.edit.data.service_id.includes(item.id))"
                text-by="name"
                value-by="id"
                multiple
                searchable
                clearable
                clearable-icon="backspace"
                highlight-matched-text
                :error="sched.enrollee.edit.servicesEmpty"
                :error-messages="'The service(s) field is required.'"
                @update:modelValue="sched.enrollee.edit.servicesEmpty = false"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            sched.enrollee.edit.data = { ...sched.enrollee.create.resetData },
                            sched.enrollee.edit.saved = false,
                            sched.enrollee.edit.deleteModal = !sched.enrollee.edit.deleteModal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        :loading="sched.enrollee.edit.saved"
                        :disabled="sched.enrollee.edit.saved"
                        @click="sched.enrollee.edit.saved = true, sched.enrollee.edit.deleteMode == 0 ? deleteEnrollee() : rescheduleEnrollee()"
                        >
                            <p class="font-normal">Confirm</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="sched.enrollee.reschedule.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Reschedule Student
                </div>
                <va-select
                v-model="sched.enrollee.reschedule.data.datetime"
                label="Date and Time(s)"
                requiredMark
                class="mb-2 w-full"
                noOptionsText="No datetime(s) available"
                :options="getRescheduleDateTime(sched.enrollee.reschedule.data)"
                text-by="name"
                :value-by="(item) => item"
                searchable
                clearable
                clearable-icon="backspace"
                highlight-matched-text
                :error="sched.enrollee.reschedule.datetimeEmpty"
                :error-messages="'The datetime(s) field is required.'"
                @update:modelValue="sched.enrollee.reschedule.datetimeEmpty = false"
                />
                <div class="flex flex-col gap-x-[7px] px-[12px] py-1.5 rounded border border-solid border-[#ECF0F1] bg-neutral-100">
                    <div>
                        <div class="shrink">
                            <div class="grid gap-y-[3px]">
                                <div class="flex mb-[5px] gap-x-[15px] text-[18px]">
                                    <div class="shrink">
                                        {{ sched.enrollee.reschedule.data.service_name }}
                                    </div>
                                </div>
                                <div class="flex gap-x-[15px] text-[13px]">
                                    <div class="shrink">
                                        Student:
                                        <span class="ml-2 va-text-secondary">
                                            {{ sched.enrollee.reschedule.data.student_name }}
                                        </span>
                                    </div>
                                    <div class="shrink">
                                        Instructor:
                                        <span class="ml-2 va-text-secondary">
                                            {{ sched.enrollee.reschedule.data.instructor_name }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-x-[15px] text-[13px]">
                                    <div class="shrink">
                                        Datetime:
                                        <span class="ml-2 va-text-secondary">
                                            {{ getDateTimeSchedule(sched.enrollee.reschedule.data) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-x-[15px] text-[13px]">
                                    <div class="shrink">
                                        Batch:
                                        <span class="ml-2 va-text-secondary">
                                            {{ sched.enrollee.reschedule.data.batch }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <va-divider v-if="sched.enrollee.reschedule.data.datetime" class="py-[5px]" />
                    <div v-if="sched.enrollee.reschedule.data.datetime">
                        <div class="shrink">
                            <div class="grid gap-y-[3px]">
                                <div class="va-title mb-2">
                                    New Schedule
                                </div>
                                <div class="flex gap-x-[15px] text-[13px]">
                                    <div class="shrink">
                                        Instructor:
                                        <span
                                        class="ml-2"
                                        :class="sched.enrollee.reschedule.data.instructor_name != sched.enrollee.reschedule.data.datetime.instructor_name ? 'va-text-success' : 'va-text-secondary'">
                                            {{ sched.enrollee.reschedule.data.datetime.instructor_name }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-x-[15px] text-[13px]">
                                    <div class="shrink">
                                        Datetime:
                                        <span class="ml-2 va-text-success">
                                            {{ getDateTimeSchedule(sched.enrollee.reschedule.data.datetime, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-x-[15px] text-[13px]">
                                    <div class="shrink">
                                        Batch:
                                        <span class="ml-2 va-text-success">
                                            {{ sched.enrollee.reschedule.data.datetime.batch }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            sched.enrollee.reschedule.data = { ...sched.enrollee.reschedule.resetData },
                            sched.enrollee.reschedule.datetimeEmpty = false,
                            sched.enrollee.reschedule.saved = false,
                            sched.enrollee.reschedule.modal = !sched.enrollee.reschedule.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        :loading="sched.enrollee.reschedule.saved"
                        :disabled="sched.enrollee.reschedule.saved"
                        @click="sched.enrollee.reschedule.saved = true, insertUpdateReschedule()"
                        >
                            <p class="font-normal">Confirm</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

</template>

<style lang="scss" scoped>
.table-crud {
    --va-form-element-default-width: 0;

    .va-input {
        display: block;
    }

    &__slot {
        th {
            vertical-align: middle;
        }
    }
}
</style>

<script>
import formatDate from '@/functions/formatdate.js';

const newEnrol = {
    name: "",
    services: [],
    date_enrolled: formatDate(new Date().getTime(), 'YYYY-MM-DD HH:mm:ss'),
    student_id: null,
}

export default {
    data () {
        return {
            sched: {
                enrollee: {
                    table: {
                        columns: [
                            { key: "name", label: "Student", sortable: true },
                            { key: "services", label: "Services", width: 80, tdAlign: "center", sortable: false },
                            { key: "date_enrolled", label: "Enrolled On", width: 125, sortable: true },
                            { key: "student_id", label: "Action", width: 60, sortable: false },
                        ],
                        perPage: this.$root.config.tblPerPage,
                        currPage: this.$root.config.tblCurrPage,
                    },
                    create: {
                        modal: false,
                        saved: false,
                        enrolleeEmpty: false,
                        servicesEmpty: false,
                        resetData: { ...newEnrol },
                        data: { ...newEnrol },
                    },
                    edit: {
                        modal: false,
                        deleteMode: 0,
                        deleteModal: false,
                        enrolleeEmpty: false,
                        servicesEmpty: false,
                        saved: false,
                        data: {},
                    },
                    reschedule: {
                        modal: false,
                        datetimeEmpty: false,
                        saved: false,
                        data: {},
                    },
                    items: [],
                    activePreviewRow: null,
                },
                active: {
                    table: {
                        columns: [
                            { key: "name", label: "Service", sortable: true },
                            { key: "instructor", label: "Instructor", sortable: true },
                            { key: "date_start", label: "Date Start", sortable: false },
                            { key: "date_end", label: "Date End", sortable: false },
                        ],
                        perPage: this.$root.config.tblPerPage,
                        currPage: this.$root.config.tblCurrPage,
                    },
                    items: [],
                },
                completed: {
                    table: {
                        columns: [
                            { key: "name", label: "Service", sortable: true },
                            { key: "instructor", label: "Instructor", sortable: true },
                            { key: "date_start", label: "Date Start", sortable: false },
                            { key: "date_end", label: "Date End", sortable: false },
                        ],
                        perPage: this.$root.config.tblPerPage,
                        currPage: this.$root.config.tblCurrPage,
                    },
                    items: [],
                },
                cancelled: {
                    table: {
                        columns: [
                            { key: "service_name", label: "Service", sortable: true },
                            { key: "name", label: "Instructor", sortable: false },
                            { key: "datetime", label: "Date and Time", sortable: false },
                        ],
                        perPage: this.$root.config.tblPerPage,
                        currPage: this.$root.config.tblCurrPage,
                    },
                    items: [],
                },
                archived: {
                    table: {
                        columns: [
                            { key: "name", label: "Student", sortable: true },
                            { key: "service", label: "Service", sortable: true },
                            { key: "date_archived", label: "Date", sortable: false },
                        ],
                        perPage: this.$root.config.tblPerPage,
                        currPage: this.$root.config.tblCurrPage,
                    },
                    items: [],
                },
                reschedule: {
                    table: {
                        columns: [
                            { key: "student_name", label: "Student", sortable: true },
                            { key: "service_name", label: "Service", sortable: true },
                            { key: "instructor_name", label: "Instructor", sortable: true },
                            { key: "datetime", label: "Date and Time", sortable: false },
                            { key: "student_id", label: "Action", sortable: false },
                        ],
                        perPage: this.$root.config.tblPerPage,
                        currPage: this.$root.config.tblCurrPage,
                    },
                    aservs: [],
                    items: [],
                },
                services: []
            },
            filtered: null,
            filter: "",
            stabs: [
                { title: 'Enrolled', icon: 'free_cancellation' },
                { title: 'Completed', icon: 'format_list_bulleted' },
                { title: 'Reschedule', icon: 'timelapse' },
                { title: 'Cancelled', icon: 'cancel_schedule_send' },
                { title: 'Archive', icon: 'recent_actors' },
            ],
            stab: 0,
        }
    },
    beforeMount () {
        this.stabs = this.stabs.filter(item => {
            return this.$root.auth.userType !== 0 ? (item.title !== 'Completed') : item;
        });

        this.stabs = this.stabs.filter(item => {
            return this.$root.auth.userType === 0 ? (item.title !== 'Cancelled' && item.title !== 'Archive' && item.title !== 'Reschedule') : item;
        });

        this.stabs = this.stabs.filter(item => {
            return this.$root.auth.userType === 2 ? (item.title !== 'Enrolled') : item;
        });

        this.sched.enrollee.table.columns = this.sched.enrollee.table.columns.filter(item => {
            return this.$root.auth.userType === 0 ? (item.key !== "student_id") : item;
        });
    },
    computed: {
        pages() {
            return this.$root.config.tblPerPage && this.$root.config.tblPerPage !== 0
            ? Math.ceil(this.filtered.length / this.$root.config.tblPerPage)
            : this.filtered.length;
        },
    },
    mounted () {
        if (this.$root.auth.userType !== 0) {
            this.getEnrollees();
            this.getEnrollments();
            this.getRescheduleEnrollments();
            this.getScheduleServiceBatches();
        } else this.getStudEnrollments();
    },
    methods: {
        getDateTimeSchedule(item, mode = 0) {
            let datetime;
            if (mode == 1) {
                datetime = this.formatDate(item.date_start + ' ' + item.time_start, 'MMM. Do YYYY (hh:mma')
                    + '-' + this.formatDate(item.date_end + ' ' + item.time_end, 'hh:mma)')
            } else {
                datetime = this.formatDate(item.day_of_week + ' ' + item.time_start, 'MMM. Do YYYY (hh:mma')
                + '-' + this.formatDate(item.day_of_week + ' ' + item.time_end, 'hh:mma)');
            }
            return datetime;
        },
        getRescheduleDateTime(datetime) {
            let scheds = this.sched.reschedule.aservs.filter(item => item.service_id == datetime.service_id && item.batch > datetime.batch);
            for (let idx in scheds) {
                let sched = scheds[idx];
                scheds[idx].id = idx;
                scheds[idx].name = this.getDateTimeSchedule(sched, 1) + " [#" + sched.batch + "]";
            }
            return scheds;
        },
        deleteEnrollee() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/enrolled_student/delete',
                data: {
                    id: this.sched.enrollee.edit.data.student_id,
                    services: this.sched.enrollee.edit.data.services,
                }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.sched.enrollee.edit.data = { ...newEnrol };
                    this.sched.enrollee.edit.deleteModal = false;
                    this.sched.enrollee.edit.saved = false;

                    this.getEnrollees();
                    this.getEnrollments();
                    this.getRescheduleEnrollments();
                    this.getScheduleServiceBatches();

                    this.sched.enrollee.activePreviewRow && this.sched.enrollee.activePreviewRow.toggleRowDetails(false);
                    this.sched.enrollee.activePreviewRow = null;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        rescheduleEnrollee() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/enrolled_student/reschedule',
                data: {
                    id: this.sched.enrollee.edit.data.student_id,
                    services: this.sched.enrollee.edit.data.services,
                }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.sched.enrollee.edit.data = { ...newEnrol };
                    this.sched.enrollee.edit.deleteModal = false;
                    this.sched.enrollee.edit.saved = false;

                    this.getEnrollees();
                    this.getEnrollments();
                    this.getRescheduleEnrollments();
                    this.getScheduleServiceBatches();

                    this.sched.enrollee.activePreviewRow && this.sched.enrollee.activePreviewRow.toggleRowDetails(false);
                    this.sched.enrollee.activePreviewRow = null;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        insertUpdateEnrollee(method) {
            if (method !== 'create' || method !== 'save') {
                let data = method === 'create' ? this.sched.enrollee.create.data : (method === 'save' && this.sched.enrollee.edit.data);
                data.modif = method === 'save' ? true : (false);

                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/enrolled_student/save',
                    data: data,
                }).then(response => {
                    if (response.data.status == 1) {
                        this.$root.prompt(response.data.text);
                        method === 'create' ? (
                            this.sched.enrollee.create.data = { ...newEnrol },
                            this.sched.enrollee.create.modal = false,
                            this.sched.enrollee.create.enrolleeEmpty = false,
                            this.sched.enrollee.create.servicesEmpty = false,
                            this.sched.enrollee.create.saved = false
                        )
                        : (
                            method === 'save' && (
                                this.sched.enrollee.edit.data = { ...newEnrol },
                                this.sched.enrollee.edit.modal = false,
                                this.sched.enrollee.edit.enrolleeEmpty = false,
                                this.sched.enrollee.edit.servicesEmpty = false,
                                this.sched.enrollee.edit.saved = false
                            )
                        );

                        this.getStudents();
                    } else this.$root.prompt(response.data.text);
                }).catch(error => {
                    this.$root.prompt(error.response.data.message);
                    let resDataError = Object.keys(error.response.data.errors);

                    if (resDataError.filter(key => key == 'student_id').length) {
                        method === 'create' ? this.sched.enrollee.create.enrolleeEmpty = true
                        : (
                            method === 'save' && (this.sched.enrollee.edit.enrolleeEmpty = true)
                        );
                    }
                    if (resDataError.filter(key => key == 'services').length) {
                        method === 'create' ? this.sched.enrollee.create.servicesEmpty = true
                        : (
                            method === 'save' && (this.sched.enrollee.edit.servicesEmpty = true)
                        );
                    }

                    method === 'create' ? this.sched.enrollee.create.saved = false
                    : (method === 'save' && (this.sched.enrollee.edit.saved = false));
                });
            } else this.$root.prompt();
        },
        insertUpdateReschedule() {
            let prevData = this.sched.enrollee.reschedule.data;
            let newData = this.sched.enrollee.reschedule.data.datetime;
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/reschedule/save',
                data: {
                    service_id: newData.service_id,
                    old_batch: prevData.batch,
                    new_batch: newData.batch,
                    student_id: prevData.student_id,
                }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.sched.enrollee.reschedule.data = {};
                    this.sched.enrollee.reschedule.modal = false;
                    this.sched.enrollee.reschedule.saved = false;

                    this.getEnrollees();
                    this.getEnrollments();
                    this.getRescheduleEnrollments();
                    this.getScheduleServiceBatches();
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        getEnrollments() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/get_enrollments',
            }).then(response => {
                if (response.data.status == 1) {
                    this.sched.archived.items = response.data.result.archived_students;
                    this.sched.cancelled.items = response.data.result.cancelled_scheds;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        getEnrollees() {
            const url = this.$root.auth.userType === 2 ? '/get_enrolled_students' : '/get_enrolled_students_for_admin';
            axios({
                method: 'GET',
                type: 'JSON',
                url: url,
            }).then(response => {
                if (response.data.status == 1) {
                    this.sched.enrollee.items = response.data.result.students;
                    this.sched.services = response.data.result.services;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        getStudEnrollments() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/student/enrollment_history',
            }).then(response => {
                if (response.data.status == 1) {
                    this.sched.active.items = response.data.result.active;
                    this.sched.completed.items = response.data.result.completed;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        getRescheduleEnrollments() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/student/reschedule',
            }).then(response => {
                if (response.data.status == 1) {
                    this.sched.reschedule.items = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        getScheduleServiceBatches() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/service/schedule/service_batches'
            }).then(response => {
                this.isLoading = false;
                if (response.data.status == 1) {
                    this.sched.reschedule.aservs = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.isLoading = false;
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    },
}
</script>
