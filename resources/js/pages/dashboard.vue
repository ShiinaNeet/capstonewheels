<template>
    <div class="h-screen">
        <navigation />

        <div class="flex items-start min-h-[calc(100vh-62px)]">
            <div
            v-if="$root.auth.userType !== 0"
            style="position: sticky; top:62px;"
            class="flex py-8 min-h-[calc(100vh-62px)]"
            :class="
                menu_open
                ? ('min-w-[250px] max-w-[250px]')
                : ('pr-1 width-[2.775rem] max-w-[2.775rem]')
            ">
                <div
                id="settings-nav-wrapper"
                :class="
                    menu_open
                    ? ('min-w-[250px] max-w-[250px]')
                    : ('width-[2.775rem] max-w-[2.775rem] overflow-x-hidden')
                ">
                    <div class="relative">
                        <h5
                        class="pl-[30px] pt-[5.5px] pb-[3px] text-base text-center"
                        :class="!menu_open && ('invisible')"
                        >
                            Management
                        </h5>
                        <div class="absolute top-0 left-[0.25rem]">
                            <va-button
                            :title="menu_open ? 'Hide menu' : 'Open menu'"
                            preset="primary"
                            :icon="menu_open ? 'menu_open' : 'menu'"
                            @click="menu_open = !menu_open"
                            />
                        </div>
                    </div>
                    <va-divider class="pr-1" />

                    <div>
                        <div
                        v-if="!menu_open"
                        class="va-title mx-3 my-2"
                        >
                            <va-icon
                            name="dashboard"
                            size="small"
                            title="Dashboard"
                            class="ml-[2px] my-[3px]"
                            :class="menu_open && ('mr-1')"
                            />
                        </div>
                        <va-sidebar-item
                        v-if="menu_open"
                        v-for="(setting, idx) in dashboard"
                        :key="idx"
                        :active="isSettingActive(setting)"
                        @click="setSettingActive(setting)"
                        >
                            <va-sidebar-item-content class="min-h-[20px!important] p-[7px!important]">
                                <va-sidebar-item-title class="text-sm select-none">
                                    {{ setting }}
                                </va-sidebar-item-title>
                            </va-sidebar-item-content>
                        </va-sidebar-item>
                        <va-divider class="pr-1" />
                    </div>

                    <div v-if="$root.auth.userType === 3">
                        <div class="va-title mx-3 my-2">
                            <va-icon
                            name="supervisor_account"
                            size="small"
                            title="Users"
                            class="ml-[2px] my-[3px]"
                            :class="menu_open && ('mr-1')"
                            />
                            <span
                            class="align-middle tracking-wider"
                            :class="!menu_open && ('hidden')"
                            >
                                Users
                            </span>
                        </div>
                        <va-sidebar-item
                        v-if="menu_open"
                        v-for="(setting, idx) in users_mngt"
                        :key="idx"
                        :active="isSettingActive(setting)"
                        @click="setSettingActive(setting)"
                        >
                            <va-sidebar-item-content class="min-h-[20px!important] p-[7px!important]">
                                <va-sidebar-item-title class="text-sm select-none">
                                    {{ setting }}
                                </va-sidebar-item-title>
                            </va-sidebar-item-content>
                        </va-sidebar-item>
                        <va-divider class="pr-1" />
                    </div>

                    <div v-if="$root.auth.userType === 3">
                        <div class="va-title mx-3 my-2">
                            <va-icon
                            name="mms"
                            size="small"
                            title="Content"
                            class="ml-[2px] my-[3px]"
                            :class="menu_open && ('mr-1')"
                            />
                            <span
                            class="align-middle tracking-wider"
                            :class="!menu_open && ('hidden')"
                            >
                                Content
                            </span>
                        </div>
                        <va-sidebar-item
                        v-if="menu_open"
                        v-for="(setting, idx) in media_mngt"
                        :key="idx"
                        :active="isSettingActive(setting)"
                        @click="setSettingActive(setting)"
                        >
                            <va-sidebar-item-content class="min-h-[20px!important] p-[7px!important]">
                                <va-sidebar-item-title class="text-sm select-none">
                                    {{ setting }}
                                </va-sidebar-item-title>
                            </va-sidebar-item-content>
                        </va-sidebar-item>
                        <va-divider class="pr-1" />
                    </div>

                    <div>
                        <div class="va-title mx-3 my-2">
                            <va-icon
                            name="tag"
                            size="small"
                            title="Maintenance"
                            class="ml-[2px] my-[3px]"
                            :class="menu_open && ('mr-1')"
                            />
                            <span
                            class="align-middle tracking-wider"
                            :class="!menu_open && ('hidden')"
                            >
                                Maintenance
                            </span>
                        </div>
                        <va-sidebar-item
                        v-if="menu_open"
                        v-for="(setting, idx) in enrol_mngt.filter(item => {
                            return $root.auth.userType !== 2 ? item : (item === enrol_mngt[1])
                        })"
                        :key="idx"
                        :active="isSettingActive(setting)"
                        @click="setSettingActive(setting)"
                        >
                            <va-sidebar-item-content class="min-h-[20px!important] p-[7px!important]">
                                <va-sidebar-item-title class="text-sm select-none">
                                    {{ setting }}
                                </va-sidebar-item-title>
                            </va-sidebar-item-content>
                        </va-sidebar-item>
                        <va-divider class="pr-1" />
                    </div>

                    <div>
                        <div class="va-title mx-3 my-2">
                            <va-icon
                            name="leaderboard"
                            size="small"
                            title="Reports"
                            class="ml-[2px] my-[3px]"
                            :class="menu_open && ('mr-1')"
                            />
                            <span
                            class="align-middle tracking-wider"
                            :class="!menu_open && ('hidden')"
                            >
                                Reports
                            </span>
                        </div>
                        <va-sidebar-item
                        v-if="menu_open"
                        v-for="(setting, idx) in reprt_mngt.filter(item => {
                            return $root.auth.userType !== 1 ? item : (item === reprt_mngt[0])
                        })"
                        :key="idx"
                        :active="isSettingActive(setting)"
                        @click="setSettingActive(setting)"
                        >
                            <va-sidebar-item-content class="min-h-[20px!important] p-[7px!important]">
                                <va-sidebar-item-title class="text-sm select-none">
                                    {{ setting }}
                                </va-sidebar-item-title>
                            </va-sidebar-item-content>
                        </va-sidebar-item>
                        <va-divider class="pr-1" />
                    </div>

                    <div v-if="$root.auth.userType === 3">
                        <div class="va-title mx-3 my-2">
                            <va-icon
                            name="restore"
                            size="small"
                            title="Utilities"
                            class="ml-[2px] my-[3px]"
                            :class="menu_open && ('mr-1')"
                            />
                            <span
                            class="align-middle tracking-wider"
                            :class="!menu_open && ('hidden')"
                            >
                                Utilities
                            </span>
                        </div>
                        <va-sidebar-item
                        v-if="menu_open"
                        v-for="(setting, idx) in bckup_mngt"
                        :key="idx"
                        :active="isSettingActive(setting)"
                        @click="setSettingActive(setting)"
                        >
                            <va-sidebar-item-content class="min-h-[20px!important] p-[7px!important]">
                                <va-sidebar-item-title class="text-sm select-none">
                                    {{ setting }}
                                </va-sidebar-item-title>
                            </va-sidebar-item-content>
                        </va-sidebar-item>
                    </div>
                </div>
            </div>
            <div class="flex w-full pt-8 min-h-[calc(100vh-62px)] overflow-x-hidden">
                <div
                id="settings-wrapper"
                >
                    <template v-if="activeSetting === 'Account'">
                        <accounts :session-id="accountId" />
                    </template>
                    <template v-if="activeSetting === 'News'">
                        <news />
                    </template>
                    <template v-if="activeSetting === 'Help'">
                        <help />
                    </template>
                    <template v-if="activeSetting === 'Requirement'">
                        <requirements />
                    </template>
                    <template v-if="activeSetting === 'Room'">
                        <rooms />
                    </template>
                    <template v-if="activeSetting === 'Service'">
                        <services />
                    </template>
                    <template v-if="activeSetting === 'Schedule'">
                        <schedules />
                    </template>
                    <template v-if="activeSetting === 'Vehicle'">
                        <vehicles />
                    </template>
                    <template v-if="activeSetting === 'Database Export'">
                        <database :mode="0" />
                    </template>
                    <template v-if="activeSetting === 'Database Import'">
                        <database :mode="1" />
                    </template>
                    <template v-if="activeSetting === 'Audit Log'">
                        <audits />
                    </template>
                    <template v-if="activeSetting === 'LTO Enrolled Students'">
                        <enrstudrep :mode="0" />
                    </template>
                    <template v-if="activeSetting === 'Failed Enrollments'">
                        <enrstudrep :mode="1" />
                    </template>
                    <template v-if="activeSetting === 'Income'">
                        <incomerep />
                    </template>
                    <template v-if="activeSetting === 'Dashboard'">
                        <dashboard />
                    </template>
                    <template v-if="activeSetting === 'Company Details'">
                        <companydetails/>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
#settings-nav-wrapper::after {
    content: "";
    position: absolute;
    top: 30px;
    bottom: 30px;
    right: 0px;
    border-right: 1px solid #DEE5F2;
}
#settings-nav-wrapper .va-sidebar-item:hover {
    /* border-color: rgba(0, 0, 0, 0.3); */
    background-color: rgba(0, 0, 0, 0.085)!important;
}
#settings-nav-wrapper .va-sidebar-item.va-sidebar-item--active {
    color: rgb(38, 40, 36)!important;
    background-color: rgba(21, 78, 193, 0.2)!important;
}
</style>

<script>
import navmenu from '@/components/navbar.vue';
import accounts_tbl from '@/components/admin/accounts.vue';
import auditlogs_tbl from '@/components/admin/auditlogs.vue';
import dashboardmenu from '@/components/admin/dashboard.vue';
import database_mngt from '@/components/admin/database.vue';
import enrstudrep_tbl from '@/components/reports/enrolledstudents.vue';
import help_tbl from '@/components/admin/help.vue';
import news_tbl from '@/components/admin/news.vue';
import incomerep_tbl from '@/components/reports/income.vue';
import companyDetails_tbl from '@/components/admin/companydetails.vue';
import requirement_tbl from '@/components/admin/requirements.vue';
import room_tbl from '@/components/admin/rooms.vue';
import service_tbl from '@/components/admin/services.vue';
import schedule_tbl from '@/components/admin/schedules.vue';
import vehicle_tbl from '@/components/admin/vehicles.vue';

export default {
    data () {
        return {
            dashboard: ['Dashboard'],
            users_mngt: ['Account'],
            media_mngt: ['News', 'Help', 'Company Details'],
            enrol_mngt: ['Schedule', 'Service', 'Requirement', 'Room', 'Vehicle'],
            reprt_mngt: ['LTO Enrolled Students', 'Failed Enrollments', 'Income'],
            bckup_mngt: ['Database Import', 'Database Export', 'Audit Log'],
            activeSetting: 'Dashboard',
            menu_open: false,
        };
    },
    components: {
        navigation: navmenu,
        accounts: accounts_tbl,
        audits: auditlogs_tbl,
        help: help_tbl,
        news: news_tbl,
        enrstudrep: enrstudrep_tbl,
        incomerep: incomerep_tbl,
        requirements: requirement_tbl,
        rooms: room_tbl,
        services: service_tbl,
        schedules: schedule_tbl,
        vehicles: vehicle_tbl,
        dashboard: dashboardmenu,
        database: database_mngt,
        companydetails: companyDetails_tbl
    },
    props: ['accountId'],
    methods: {
        isSettingActive(setting) {
            return this.activeSetting === setting;
        },
        setSettingActive(setting) {
            this.$root.config.tblCurrPage = 1;
            this.activeSetting = setting;
        },
    }
}
</script>
