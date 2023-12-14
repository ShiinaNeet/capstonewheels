<template>
    <div class="h-screen">
        <navigation />

        <div class="flex items-start">
            <div class="flex w-full py-8" style="position: sticky; top:62px;">
                <div
                v-if="enrollment.window === 0 && mode !== 'schedules'"
                class="shrink mx-auto lg:w-[min(100%,85vw)] sm:w-[min(100%,105vw)]"
                >
                    <div class="grid grid-cols-2 gap-4" id="service-list">
                        <div
                        v-for="(item, idx) in services"
                        :key="idx"
                        >
                            <va-card>
                                <va-card-block
                                class="min-w-[225px] flex-nowrap group"
                                horizontal
                                >
                                    <div class="flex-auto overflow-hidden">
                                        <va-card-title class="p-[10px!important] pb-[7px!important] border-b border-solid border-[#F0F0F0]">
                                            <p class="text-[11px] whitespace-nowrap">
                                                {{
                                                    serv.types[
                                                        $root.arrayFind(
                                                            serv.types, (x) => x.value === parseInt(item.type)
                                                        )
                                                    ].label
                                                }}
                                                Service #{{ item.id }} - {{ item.name }}
                                            </p>
                                        </va-card-title>
                                        <div class="flex">
                                            <va-scroll-container
                                            class="min-w-[250px!important] lg:max-h-[180px!important] sm:max-h-[150px!important]"
                                            color="primary"
                                            rtl
                                            vertical
                                            >
                                                <va-card-content class="flex p-[10px!important]">
                                                    <div class="w-[60%] mr-[10px]">
                                                        <div class="flex mb-[10px]">
                                                            <div class="mr-[10px]">
                                                                <div class="va-title mb-2 tracking-wider opacity-80">
                                                                    Price
                                                                </div>
                                                                <div>
                                                                    ₱ {{ parseFloat(item.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="va-title mb-2 tracking-wider opacity-80">
                                                                    Duration
                                                                </div>
                                                                <div>
                                                                    {{ item.duration }} Hours
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="va-title mb-2 tracking-wider opacity-80">
                                                            Description
                                                        </div>
                                                        <div
                                                        class="mb-[20px]"
                                                        :class="!item.description && ('opacity-50')"
                                                        >
                                                            {{ item.description ? item.description : 'No description available' }}
                                                        </div>
                                                    </div>
                                                    <div class="w-[40%] overflow-hidden">
                                                        <div class="va-title mb-2 tracking-wider opacity-80">
                                                            Requirements
                                                        </div>
                                                        <div class="mb-2">
                                                            <va-chip
                                                            v-for="(req, idx) in item.requirements"
                                                            :key="idx"
                                                            class="mr-[3px] mb-1"
                                                            color="secondary"
                                                            size="small"
                                                            readonly
                                                            square
                                                            >
                                                                {{
                                                                    requirements[
                                                                        $root.arrayFind(
                                                                            requirements, (item) => item.id === req
                                                                        )
                                                                    ].name
                                                                }}
                                                            </va-chip>
                                                        </div>
                                                    </div>
                                                </va-card-content>
                                            </va-scroll-container>
                                            <va-image
                                            class="
                                                lg:min-w-[180px] lg:max-w-[180px!important]
                                                sm:min-w-[150px] sm:max-w-[150px!important]
                                                lg:min-h-[180px] lg:max-h-[180px!important]
                                                sm:min-h-[150px] sm:max-h-[150px!important]
                                                flex-grow-0 flex-shrink-0 basis-52
                                            "
                                            :src="$root.forgeImageFile(item.image[0], 'services', false)"
                                            :placeholder-src="$root.forgeImageFile(null, null, false)"
                                            >
                                                <div class="w-full h-full z-[1] bg-white opacity-[0] group-hover:opacity-[0.3]"></div>
                                                <div
                                                class="absolute z-[2] left-[7px] bottom-[7px] right-[7px] group-hover:block"
                                                :class="
                                                    (enrollment.service.id !== item.id && enrollment.saved.a) && ('invisible'),
                                                    (enrollment.service.id === item.id && enrollment.saved.a) ? 'block' : 'hidden'
                                                "
                                                >
                                                    <va-button
                                                    class="w-full rounded-[3px!important]"
                                                    :loading="enrollment.saved.a"
                                                    :disabled="enrollment.saved.a"
                                                    @click="
                                                        enrollment.saved.a = true,
                                                        enrollment.service = item,
                                                        getSchedulesForService()
                                                    ">
                                                        Enroll
                                                    </va-button>
                                                </div>
                                                <template #error>
                                                    <img
                                                    class="w-full h-full z-[1] bg-white opacity-[1] group-hover:opacity-[0.5]"
                                                    :src="$root.forgeImageFile(null, null, false)"
                                                    />
                                                    <div
                                                    class="absolute z-[2] left-[7px] bottom-[7px] right-[7px] group-hover:block"
                                                    :class="
                                                        (enrollment.service.id !== item.id && enrollment.saved.a) && ('invisible'),
                                                        (enrollment.service.id === item.id && enrollment.saved.a) ? 'block' : 'hidden'
                                                    "
                                                    >
                                                        <va-button
                                                        class="w-full rounded-[3px!important]"
                                                        :loading="enrollment.saved.a"
                                                        :disabled="enrollment.saved.a"
                                                        @click="
                                                            enrollment.saved.a = true,
                                                            enrollment.service = item,
                                                            getSchedulesForService()
                                                        ">
                                                            Enroll
                                                        </va-button>
                                                    </div>
                                                </template>
                                            </va-image>
                                        </div>
                                    </div>
                                </va-card-block>
                            </va-card>
                        </div>
                    </div>
                </div>
                <div
                v-if="enrollment.window === 1 || mode === 'schedules'"
                :id="mode !== 'schedules' ? 'enrollment-table' : 'enrollment-schedule-table'"
                class="shrink mx-auto lg:w-[min(100%,85vw)] sm:w-[min(100%,105vw)] px-2.5 py-2.5 bg-white rounded"
                >
                    <div class="relative">
                        <div
                        class="va-title absolute left-0 mx-auto text-center"
                        :class="mode !== 'schedules' && ('right-0')"
                        >
                            Schedule
                        </div>
                        <div class="absolute left-0 mx-auto text-center">
                            <div
                            v-if="mode !== 'schedules'"
                            class="inline-flex"
                            >
                                <div class="mr-[0.75em]">
                                    <va-button
                                    icon="arrow_back"
                                    preset="secondary"
                                    border-color="primary"
                                    title="Back to services"
                                    @click="enrollment.window = 0"
                                    />
                                </div>
                                <div class="my-auto text-left">
                                    <div class="font-medium">
                                        {{ enrollment.service.name }}
                                    </div>
                                    <div class="inline-flex">
                                        <div class="mr-[0.65em] text-[13px]">
                                            <span class="mr-1 font-semibold">Price:</span> ₱ {{ parseFloat(enrollment.service.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                        </div>
                                        <div class="text-[13px]">
                                            <span class="mr-1 font-semibold">Duration:</span> {{ enrollment.service.duration }} Hrs
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-[1.886em] left-0 right-0 mx-auto text-center">
                            <va-divider />
                        </div>
                    </div>
                    <full-calendar :options="calendarOptions" />
                </div>
            </div>
        </div>
    </div>

    <va-modal
    v-model="enrollment.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Enroll Service
                </div>
                <div class="flex items-center gap-x-[7px] px-[15px] py-1.5 rounded border border-solid border-[#ECF0F1] bg-neutral-100">
                    <div
                    v-for="(enrollment, i) in cservs"
                    :key="i"
                    class="shrink"
                    >
                        <div class="font-medium mb-2.5 text-[20px]">
                            {{ enrollment.service.name }}
                        </div>
                        <div class="grid gap-y-[3px]">
                            <div class="flex gap-x-[15px] text-[15px]">
                                <div class="shrink">
                                    {{ getDateTimeSchedule(enrollment) }}
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
                            <div class="flex gap-x-[15px] text-[13px]">
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
                        </div>
                    </div>
                </div>
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            cservs = [],
                            enrollment.saved.b = false,
                            enrollment.modal = !enrollment.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        :loading="enrollment.saved.b"
                        :disabled="enrollment.saved.b"
                        @click="
                            enrollment.saved.b = true,
                            $root.redirectToPage('/enrollment_form/' + cservs[0].service_id + '/' + cservs[0].batch)
                        ">
                            <p class="font-normal">Confirm</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>
</template>

<style>
#service-list .va-chip__content {
    min-width: 103px;
    overflow-x: hidden;
    text-overflow: ellipsis!important;
    white-space: nowrap;
}
#service-list .va-image__content img  {
    /* border-top-right-radius: 4px; */
    border-bottom-right-radius: 4px;
}
#service-list .va-image__error img  {
    /* border-top-right-radius: 4px; */
    border-bottom-right-radius: 4px;
}
/* #enrollment-table .fc-daygrid-event {
    white-space: unset;
} */
#enrollment-table .fc-header-toolbar .fc-toolbar-chunk:first-child {
    visibility: hidden;
}
</style>

<script>
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

import navmenu from '@/components/navbar.vue';
import formatDate from '@/functions/formatdate.js';

export default {
    data () {
        const serv = {
            types: [
                { label: "Room", value: 0 },
                { label: "Vehicle", value: 1 }
            ]
        };

        return {
            serv,
            enrollment: {
                window: 0,
                modal: false,
                saved: {
                    a: false,
                    b: false,
                },
                service: {},
            },
            calendarOptions: {
                plugins: [ dayGridPlugin, interactionPlugin ],
                dayMaxEvents: true,
                initialView: 'dayGridMonth',
                headerToolbar: {
                    start: this.mode === 'schedules' ? ('title') : 'today prev,next',
                    center: this.mode !== 'schedules' ? ('title') : '',
                    end: 'today prev,next',
                },
                businessHours: {
                    daysOfWeek: this.$root.config.calendar.days,
                },
                contentHeight: 627,
                eventClick: this.mode !== 'schedules' && this.dateSelect,
                events: []
            },
            isModalVisible: false,
            requirements: [],
            services: [],
            cservs: []
        }
    },
    props: ['mode'],
    components: {
        FullCalendar,
        navigation: navmenu
    },
    mounted () {
        this.mode !== 'schedules' && this.getServices();
        this.mode === 'schedules' && this.getSchedulesForService();
    },
    methods: {
        getDateTimeSchedule(item) {
            return this.formatDate(item.day_of_week + ' ' + item.time_start, 'MMM. Do YYYY (hh:mma')
                + '-' + this.formatDate(item.day_of_week + ' ' + item.time_end, 'hh:mma)');
        },
        dateSelect(e) {
            const serv = e.event;
            const eserv = serv.extendedProps;

            const today = new Date();
            const eservDate = new Date(eserv.day_of_week);
            if (eservDate.setHours(0, 0, 0, 0) < today.setHours(0, 0, 0, 0)) {
                this.$root.prompt("Schedule is no longer available");
            } else {
                if (eserv.max_capacity !== eserv.stud_count) {
                    axios({
                        method: 'POST',
                        type: 'JSON',
                        url: '/student/services',
                        data: { day_of_week: eserv.day_of_week }
                    }).then(response => {
                        if (response.data.status == 1) {
                            this.cservs = response.data.result.filter(item => {
                                return (
                                    item.id === parseInt(serv.id) &&
                                    item.service_id === eserv.service_id &&
                                    item.time_start === eserv.time_start &&
                                    item.time_end === eserv.time_end
                                );
                            });
                            this.enrollment.modal = true;
                        } else this.$root.prompt(response.data.text);
                    }).catch(error => {
                        this.$root.prompt(error.response.data.message);
                    });
                } else this.$root.prompt("Schedule is at max capacity");
            }
        },
        getSchedulesForService() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/service/schedules',
                data: {
                    service_id: this.mode !== 'schedules' ? this.enrollment.service.id : 0,
                    enrollment: this.mode !== 'schedules' ? true : false,
                }
            }).then(response => {
                if (response.data.status == 1) {
                    this.calendarOptions.events = response.data.result;
                    this.enrollment.saved.a = false;
                    this.enrollment.saved.b = false;

                    this.enrollment.window = 1;
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
                method: 'GET',
                type: 'JSON',
                url: '/services/sort/created_at'
            }).then(response => {
                if (response.data.status == 1) {
                    this.services = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    }
}
</script>
