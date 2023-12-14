<template>
    <div class="mx-5 mb-2 px-2.5 py-2.5 bg-white rounded">
        <div class="relative">
            <div class="va-title absolute">
                Schedule
            </div>
            <div class="absolute w-[50%] left-0 right-0 mx-auto text-center">
                <va-button
                v-if="!isCreateDisabled"
                class="mr-[0.25em]"
                icon="date_range"
                preset="secondary"
                border-color="primary"
                title="Create Schedule"
                :disabled="schedule.dates.length == 0"
                @click="
                    $root.auth.userType === 1 && (
                        schedule.instructor_id = instructors[
                                this.$root.arrayFind(
                                    instructors, (item) => item.email === $root.auth.email
                                )
                            ].id
                    ),
                    isModalVisible = true
                ">
                    Create
                </va-button>
                <va-button
                v-if="!isUpdateDisabled"
                class="mr-[0.25em]"
                icon="edit_calendar"
                preset="secondary"
                border-color="primary"
                title="Modify Schedule"
                :disabled="schedule.dates.length == 0"
                @click="update.modal = true"
                >
                    Modify
                </va-button>
                <va-button
                v-if="!isCancelDisabled"
                class="mr-[0.25em]"
                icon="event_busy"
                preset="secondary"
                border-color="primary"
                title="Cancel Schedule"
                :disabled="schedule.dates.length == 0"
                @click="cancel.modal = true"
                >
                    Cancel
                </va-button>
            </div>
            <div class="absolute top-[1.886em] left-0 right-0 mx-auto text-center">
                <va-divider />
            </div>
        </div>
        <full-calendar :options="calendarOptions" />
    </div>
    <div class="mx-5 mb-5">
        <div class="va-title text-neutral-400">
            Input fields with
            <span class="text-sm leading-[0.25rem]">*</span>
            are required
        </div>
    </div>

    <va-modal
    v-model="isModalVisible"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Create Schedule
                </div>
                <va-select
                v-model="schedule.service_id"
                @update:modelValue="getScheduleBatch"
                class="mb-2 w-full"
                label="Service"
                requiredMark
                noOptionsText="No service(s) available"
                :options="services"
                searchable
                text-by="name"
                value-by="id"
                >
                    <template v-if="isBatchButtonVisible" #append>
                        <va-icon
                        :class="batches.length == 0 && ('pointer-events-none')"
                        :name="schedule.is_new_batch ? 'playlist_add_check' : 'playlist_add'"
                        :title="schedule.is_new_batch ? 'Same Batch' : 'New Batch'"
                        size="32px"
                        :color="schedule.is_new_batch ? '#154EC1' : '#767c88'"
                        @click="schedule.is_new_batch = !schedule.is_new_batch, schedule.batch = null"
                        />
                    </template>
                </va-select>
                <div class="flex w-full gap-x-3">
                    <div
                    class="flex justify-between"
                    :class="serviceType == 0 ? 'w-1/4' : 'w-full'"
                    >
                        <va-select
                        v-model="schedule.batch"
                        class="mb-2 w-full"
                        label="Batch"
                        requiredMark
                        noOptionsText="No batch(s) available"
                        :disabled="schedule.is_new_batch"
                        :options="batches"
                        :text-by="(sched) => formatDate(sched.date_start, 'MMM. Do YYYY', 'n/a') + ' - ' + formatDate(sched.date_end, 'MMM. Do YYYY', 'n/a')"
                        value-by="batch"
                        />
                    </div>
                    <div
                    v-if="serviceType == 0"
                    class="flex w-1/4 justify-between"
                    >
                        <va-counter
                        v-model="schedule.max_capacity"
                        label="Enrollee Limit"
                        class="mb-2 w-full"
                        manual-input
                        :width="'100%'"
                        :min="1"
                        />
                    </div>
                </div>
                <va-select
                v-model="schedule.instructor_id"
                class="w-full"
                label="Instructor"
                requiredMark
                :options="instructors"
                searchable
                :disabled="$root.auth.userType === 1"
                :text-by="(user) => (user.firstname ? user.firstname + ' ' + user.lastname : user.email)"
                value-by="id"
                />
                <va-divider v-if="schedule.dates.length" />
                <div>
                    <div
                    v-for="(sched, i) in schedule.dates"
                    :key="i"
                    >
                        <div class="flex items-center gap-x-[7px] mb-2 px-[15px] py-1.5 rounded border border-solid border-[#ECF0F1] bg-neutral-100">
                            <div class="shrink">
                                <div class="grid gap-y-[3px]">
                                    <div class="flex gap-x-[15px] text-[13px]">
                                        <div class="shrink">
                                            Schedule:
                                            <span class="ml-2 va-text-secondary">
                                                {{ formatDate(sched.day_of_week, 'MMM. Do, YYYY', 'n/a') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <va-time-input
                        v-model="sched.time_start_formatted"
                        class="mb-2 w-full"
                        label="Time Start"
                        requiredMark
                        :icon="'light_mode'"
                        ampm
                        />
                        <va-time-input
                        v-model="sched.time_end_formatted"
                        class="mb-2 w-full"
                        label="Time End"
                        requiredMark
                        :icon="'dark_mode'"
                        ampm
                        />
                    </div>
                </div>
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            isModalVisible = false,
                            schedule = { saved: false, dates: [], is_new_batch: false },
                            batches = []
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="schedule.saved"
                        :disabled="schedule.saved"
                        @click="schedule.saved = true, saveSchedule()"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="update.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Modify Schedule
                </div>
                <div class="flex items-center gap-x-[7px] mb-2 px-[15px] py-1.5 rounded border border-solid border-[#ECF0F1] bg-neutral-100">
                    <div class="shrink">
                        <div class="grid gap-y-[3px]">
                            <div class="flex gap-x-[15px] text-[13px]">
                                <div class="shrink">
                                    Schedule:
                                    <span class="ml-2 va-text-secondary">
                                        {{ formatDate(schedule.dates[0].day_of_week, 'MMM. Do, YYYY', 'n/a') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <va-select
                v-model="update.scheduleId"
                class="mb-2 w-full"
                label="Service"
                requiredMark
                :options="filteredServices"
                text-by="title"
                value-by="id"
                />
                <va-date-input
                v-model="update.newDate"
                class="mb-2 w-full"
                label="New Date"
                requiredMark
                clearable
                :format="formatDatePicker"
                />
                <div class="flex w-full gap-x-3">
                    <div class="flex w-1/4 justify-between">
                        <va-time-input
                        v-model="update.timeStart"
                        class="mb-2 w-full"
                        label="Time Start"
                        requiredMark
                        :icon="'light_mode'"
                        ampm
                        />
                    </div>
                    <div class="flex w-1/4 justify-between">
                        <va-time-input
                        v-model="update.timeEnd"
                        class="mb-2 w-full"
                        label="Time End"
                        requiredMark
                        :icon="'dark_mode'"
                        ampm
                        />
                    </div>
                </div>
                <va-input
                type="textarea"
                v-model="update.reason"
                label="Reason"
                class="w-full force-noresize"
                requiredMark
                maxlength="255"
                :counter="!!(update.reason && update.reason.length > 0)"
                :max-length=255
                :min-rows="5"
                :max-rows="5"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            update.saved = false,
                            update.newDate = null,
                            update.reason = null,
                            update.scheduleId = null,
                            update.timeStart = null,
                            update.timeEnd = null,
                            update.modal = false
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="update.saved"
                        :disabled="update.saved"
                        @click="
                            update.saved = true,
                            updateSchedule()
                        ">
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="cancel.modal"
    @cancel="cancel.reason = null, cancel.schedules = []"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Cancel Service
                </div>
                <div class="flex items-center gap-x-[7px] mb-2 px-[15px] py-1.5 rounded border border-solid border-[#ECF0F1] bg-neutral-100">
                    <div class="shrink">
                        <div class="grid gap-y-[3px]">
                            <div class="flex gap-x-[15px] text-[13px]">
                                <div class="shrink">
                                    Schedule:
                                    <span
                                    v-if="schedule.dates.length == 1"
                                    class="ml-2 va-text-secondary"
                                    >
                                        {{ formatDate(schedule.dates[0].day_of_week, 'MMM. Do, YYYY', 'n/a') }}
                                    </span>
                                    <span
                                    v-else
                                    class="ml-2 va-text-secondary"
                                    >
                                        {{ formatDate(schedule.dates[0].day_of_week, 'MMM. Do, YYYY', 'n/a') }} - {{ formatDate(schedule.dates.slice(-1)[0].day_of_week, 'MMM. Do, YYYY', 'n/a') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <va-select
                v-model="cancel.schedules"
                class="mb-2 w-full"
                label="Services"
                requiredMark
                :options="filteredServices"
                multiple
                text-by="title"
                value-by="id"
                >
                    <template #content="{ value }">
                        <va-chip
                        v-for="(val, i) in value"
                        :key="i"
                        size="small"
                        class="mr-1 my-1"
                        closeable
                        @update:modelValue="deleteSchedule(i)"
                        >
                            {{ val.title }}
                        </va-chip>
                    </template>
                </va-select>
                <va-input
                type="textarea"
                v-model="cancel.reason"
                label="Reason"
                class="w-full force-noresize"
                requiredMark
                maxlength="255"
                :counter="!!(cancel.reason && cancel.reason.length > 0)"
                :max-length=255
                :min-rows="5"
                :max-rows="5"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            cancel.modal = false,
                            cancel.reason = null,
                            cancel.schedules = [],
                            cancel.modal = false
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="cancel.saved"
                        :disabled="cancel.saved"
                        @click="
                            cancel.saved = true,
                            cancelSchedule()
                        ">
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>

    </va-modal>
</template>

<style>
.va-title {
    letter-spacing: unset;
}
.fc-toolbar-chunk {
    z-index: 2;
}
.fc .fc-toolbar-title {
    font-size: 1.2rem;
    line-height: 1.2;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--va-primary);
    padding-top: 0.35em;
}
.fc .fc-toolbar.fc-header-toolbar {
    margin-bottom: 0.8em;
}
.fc .fc-button,
.fc .fc-button-group .fc-button {
    font-size: var(--va-button-font-size);
    font-weight: var(--va-button-font-weight);
    padding: 7px 10px;
    line-height: var(--va-button-line-height);
    border-radius: var(--va-button-border-radius);
    letter-spacing: var(--va-button-letter-spacing);
    min-height: 2.05em;
    min-width: var(--va-button-size);
    border-width: var(--va-button-bordered-border);
    border-style: var(--va-button-bordered-style);
    text-transform: capitalize;
}
.fc .fc-button-group .fc-button {
    padding-top: 0.4em;
    padding-bottom: 0.4875em;
}
.fc-direction-ltr .fc-button-group > .fc-button:not(:first-child) {
    border-left: 0;
}
.fc-direction-ltr .fc-button-group > .fc-button:not(:last-child) {
    border-right: 0;
}
.fc .fc-button-primary{
    background-color: var(--va-primary);
    border-color: var(--va-primary);
    color: var(--va-primary);
    background: transparent;
}
.fc .fc-button-primary:disabled {
    background-color: var(--va-secondary);
    border-color: var(--va-secondary);
    color: var(--va-secondary);
    background: transparent;
}
.fc .fc-button-primary:hover {
    background-color: rgba(21,78,193,0.07);
    border-color: var(--va-primary);
    color: var(--va-primary);
}
.fc .fc-button-primary:disabled:hover {
    border-color: var(--va-secondary);
    background: transparent;
    color: var(--va-secondary);
}
.fc .fc-button-primary:focus:active {
    background-color: unset!important;
    border-color: var(--va-primary)!important;
    color: var(--va-primary)!important;
}
.fc .fc-button-primary:focus:active,
.fc .fc-button-primary:focus {
    box-shadow: none!important;
}
.fc .fc-button .fc-icon {
    font-size: 1.2em;
    font-weight: 700;
}
.fc .fc-button:hover {
    opacity: 0.85;
}
.fc .fc-button:disabled:hover {
    opacity: 0.65;
}
.fc .fc-view-harness {
    z-index: 0;
}
.fc th {
    color: #FFFFFF;
    background-color: var(--va-primary);
    vertical-align: middle;
}
.fc .fc-scrollgrid-section, .fc .fc-scrollgrid-section table, .fc .fc-scrollgrid-section > td {
    height: 20px;
}
.fc .fc-col-header-cell-cushion {
    font-size: 13px;
}
.fc-h-event .fc-event-title {
    pointer-events: none;
}
</style>

<script>
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

import formatDate from '@/functions/formatdate.js';
import moment from 'moment';

export default {
    components: {
        FullCalendar
    },
    data () {
        return {
            calendarOptions: {
                plugins: [ dayGridPlugin, interactionPlugin ],
                dayMaxEvents: true,
                initialView: 'dayGridMonth',
                headerToolbar: {
                    start: 'title',
                    center: '',
                    end: 'today prev,next',
                },
                businessHours: {
                    daysOfWeek: this.$root.config.calendar.days,
                },
                contentHeight: 627,
                selectable: true,
                select: this.dateSelect,
                events: []
            },
            isCreateDisabled: true,
            isUpdateDisabled: true,
            isCancelDisabled: true,
            isLoading: false,
            isModalVisible: false,
            isBatchButtonVisible: false,
            batches: [],
            instructors: [],
            filteredServices: [],
            services: [],
            schedule: {
                batch: null,
                dates: [],
                is_new_batch: false,
                saved: false,
            },
            cancel: {
                loading: false,
                modal: false,
                saved: false,
                reason: null,
                schedules: []
            },
            update: {
                loading: false,
                modal: false,
                saved: false,
                newDate: null,
                reason: null,
                scheduleId: null,
                timeStart: null,
                timeEnd: null
            },
            serviceType: null,
        };
    },
    watch: {
        'schedule.service_id': function(value) {
            if (value) {
                var index = this.$root.arrayFind(this.services, (item) => item.id === value);
                this.serviceType = this.services[index].type;
            } else this.serviceType = null;
        }
    },
    mounted () {
        this.getInstructors();
        this.getSchedules();
        this.getServices();
    },
    methods: {
        cancelSchedule() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/service/schedule/cancel',
                data: { reason: this.cancel.reason, schedules: this.cancel.schedules }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.schedule = {
                        saved: false,
                        dates: [],
                        is_new_batch: false
                    };
                    this.cancel = {
                        loading: false,
                        modal: false,
                        saved: false,
                        reason: null,
                        schedules: []
                    };
                    this.isCreateDisabled = true;
                    this.isUpdateDisabled = true;
                    this.isCancelDisabled = true;
                    this.getSchedules();
                    this.getServices();
                } else this.$root.prompt(response.data.text);
                this.cancel.loading = false;
                this.cancel.saved = false;
            }).catch(error => {
                this.cancel.loading = false;
                this.cancel.saved = false;
                this.$root.prompt(error.response.data.message);
            });
        },
        updateSchedule() {
            if (!this.update.newDate) return this.$root.prompt("The new date field is required.");
            if (!this.update.timeStart) return this.$root.prompt("The time start field is required.");
            if (!this.update.timeEnd) return this.$root.prompt("The time end field is required.");

            var newDate = this.formatDate(this.update.newDate, 'YYYY-MM-DD');
            var timeStart = this.formatDate(this.update.timeStart.getTime(), 'HH:mm:ss');
            var timeEnd = this.formatDate(this.update.timeEnd.getTime(), 'HH:mm:ss');
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/service/schedule/update',
                data: {
                    id: this.update.scheduleId,
                    new_date: newDate,
                    reason: this.update.reason,
                    time_start: timeStart,
                    time_end: timeEnd
                }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.schedule = {
                        saved: false,
                        dates: [],
                        is_new_batch: false
                    };
                    this.update = {
                        modal: false,
                        saved: false,
                        newDate: null,
                        reason: null,
                        scheduleId: null,
                        timeStart: null,
                        timeEnd: null
                    };
                    this.isCreateDisabled = true;
                    this.isUpdateDisabled = true;
                    this.isCancelDisabled = true;
                    this.getSchedules();
                    this.getServices();
                } else this.$root.prompt(response.data.text);
                this.update.loading = false;
                this.update.saved = false;
            }).catch(error => {
                this.update.loading = false;
                this.update.saved = false;
                this.$root.prompt(error.response.data.message);
            });
        },
        dateSelect(e) {
            var dates = [];
            dates.push({ day_of_week: e.startStr, time_start: null, time_end: null });

            var currentDate = moment(e.startStr).startOf('day');
            var lastDate = moment(e.endStr).startOf('day');

            // creating an array of dates between a date range
            while(currentDate.add(1, 'days').diff(lastDate) < 0) {
                var date = { day_of_week: currentDate.clone().format('YYYY-MM-DD'),
                    time_start_formatted: null, time_end_formatted: null }; dates.push(date);
            }
            this.schedule = { dates: dates, max_capacity: 1 };

            // Filter events to find events on the clicked date
            var filteredEvents = this.calendarOptions.events.filter((event) => {
                return dates.some((date) => date.day_of_week === event.date);
            });
            if (filteredEvents.length > 0) {
                // Events found on the clicked date
                this.isCreateDisabled = false;
                this.isCancelDisabled = false;
                if (dates.length == 1) this.isUpdateDisabled = false;
                this.filteredServices = filteredEvents;
            } else {
                // No events on the clicked date
                this.isCreateDisabled = false;
                this.isUpdateDisabled = true;
                this.isCancelDisabled = true;
            }
        },
        deleteSchedule(index) {
            this.cancel.schedules = this.cancel.schedules.filter((v) => v !== this.cancel.schedules[index]);
        },
        saveSchedule() {
            var hasNoTime = false;
            // adding new fields formatted value
            for(var i = 0; i < this.schedule.dates.length; i++) {
                if (!this.schedule.dates[i].time_start_formatted || !this.schedule.dates[i].time_end_formatted) {
                    hasNoTime = true; break;
                }
                this.schedule.dates[i].time_start = this.formatDate(this.schedule.dates[i].time_start_formatted.getTime(), 'HH:mm:ss');
                this.schedule.dates[i].time_end = this.formatDate(this.schedule.dates[i].time_end_formatted.getTime(), 'HH:mm:ss');
            }
            if (hasNoTime) {
                this.schedule.saved = false;
                return this.$root.prompt("Time start/end field is required");
            }

            axios({
                method: 'POST',
                type: 'JSON',
                url: '/service/schedule/save',
                data: this.schedule
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.schedule = {
                        saved: false,
                        dates: [],
                        is_new_batch: false
                    };
                    this.isCreateDisabled = true;
                    this.isUpdateDisabled = true;
                    this.isCancelDisabled = true;
                    this.isModalVisible = false;
                    this.getSchedules();
                    this.getServices();
                } else {
                    this.$root.prompt(response.data.text);
                    this.schedule.saved = false;
                }
            }).catch(error => {
                this.schedule.saved = false;
                this.$root.prompt(error.response.data.message);
            });
        },
        getInstructors() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/instructors'
            }).then(response => {
                if (response.data.status == 1) {
                    this.instructors = response.data.result;
                    if (this.$root.auth.userType === 1)
                        // this.schedule.instructor_id = this.instructors.filter(item => item.email === this.$root.auth.email);
                        this.schedule.instructor_id = this.instructors[
                            this.$root.arrayFind(this.instructors, (item) => item.email === this.$root.auth.email)
                        ].id;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        getScheduleBatch() {
            this.schedule.is_new_batch = true;
            this.isBatchButtonVisible = false;
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/service/schedule/batches',
                data: { service_id: this.schedule.service_id }
            }).then(response => {
                if (response.data.status == 1) {
                    if (response.data.result.length != 0) {
                        this.schedule.is_new_batch = false;
                        this.isBatchButtonVisible = true;
                    }
                    this.batches = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        getSchedules() {
            this.isLoading = true;

            axios({
                method: 'POST',
                type: 'JSON',
                url: '/service/schedules',
                data: { service_id: 0 }
            }).then(response => {
                this.isLoading = false;
                if (response.data.status == 1) {
                    this.calendarOptions.events = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.isLoading = false;
                this.$root.prompt(error.response.data.message);
            });
        },
        getServices() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/services/available'
            }).then(response => {
                this.isLoading = false;
                if (response.data.status == 1) {
                    this.services = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.isLoading = false;
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDatePicker(date) {
            return this.formatDate(date, 'MMMM DD, YYYY', 'Invalid Date');
        },
        formatDate
    }
}
</script>
