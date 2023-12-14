<template>
    <div class="mx-5 mb-2 px-2.5 pb-2.5 bg-white rounded">
        <va-data-table
        id="data-table"
        :items="vehicles"
        :columns="auto.tblColumns.filter(item => { return $root.auth.userType === 1 ? (item.key !== 'id') : item })"
        :per-page="$root.config.tblPerPage"
        :current-page="$root.config.tblCurrPage"
        no-data-html="No vehicle(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        >
            <template #headerAppend>
                <tr class="table-crud__slot">
                    <th
                    v-for="key in Object.keys(createVehicle.data).filter(item => { return $root.auth.userType === 1 ? (item !== 'id') : item })"
                    :key="key"
                    class="py-1 pr-1"
                    >
                        <va-input
                        v-if="key.includes('name')"
                        v-model="filter"
                        placeholder="Search..."
                        >
                            <template #appendInner>
                                <va-icon name="search" color="#C2C2C2" />
                            </template>
                        </va-input>
                        <va-button
                        v-if="key.includes('id') && $root.auth.userType !== 1"
                        preset="primary"
                        icon="post_add"
                        @click="createVehicle.modal = !createVehicle.modal"
                        >
                            Add
                        </va-button>
                        <va-button
                        v-if="key.includes('created_at') && $root.auth.userType === 1"
                        class="invisible"
                        preset="primary"
                        disabled
                        />
                    </th>
                </tr>
            </template>

            <template #cell(name)="{ value }">
                {{ value }}
            </template>
            <template #cell(transmission)="{ value }">
                <va-badge
                :text="auto.gearTypes[value].label"
                :color="auto.gearTypes[value].color"
                />
            </template>
            <template #cell(capacity)="{ value }">
                {{ value }}
            </template>
            <template #cell(description)="{ row, isExpanded }">
                <va-button
                class="w-full"
                preset="secondary"
                size="small"
                :icon="isExpanded ? 'va-arrow-up': 'va-arrow-down'"
                @click="row.toggleRowDetails(), activePreviewRow = row"
                >
                    {{ !isExpanded ? 'Show': 'Hide' }}
                </va-button>
            </template>
            <template #expandableRow="{ rowData }">
                <div
                class="p-2"
                id="table-row-desc"
                >
                    <va-input
                    type="textarea"
                    :model-value="rowData.description"
                    placeholder="No description available"
                    readonly
                    autosize
                    outline
                    />
                </div>
            </template>
            <template #cell(created_at)="{ value }">
                {{ formatDate(value, 'MMM. Do YYYY', 'Invalid Date') }}
            </template>
            <template #cell(id)="{ rowData }">
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Edit"
                preset="plain"
                icon="edit"
                @click="editVehicle.data = { ...rowData }, editVehicle.modal = !editVehicle.modal"
                />
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Delete"
                preset="plain"
                icon="delete"
                @click="editVehicle.data = { ...rowData }, editVehicle.deleteModal = !editVehicle.deleteModal"
                />
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(vehicles)">
                    <td
                    id="pagination"
                    :colspan="auto.tblColumns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="$root.config.tblCurrPage"
                            :pages="filter == '' ? $root.tblPagination(vehicles) : (pages, $root.config.tblCurrPage = 1)"
                            input
                            />
                        </div>
                    </td>
                </tr>
            </template>
        </va-data-table>
    </div>
    <div class="mx-5 mb-5">
        <div class="va-title text-neutral-400">
            Input fields with
            <span class="text-sm leading-[0.25rem]">*</span>
            are required
        </div>
    </div>

    <va-modal
    v-model="createVehicle.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Add Vehicle
                </div>
                <va-input
                v-model="createVehicle.data.name"
                label="Name"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The name field is required.']"
                :error="createVehicle.nameEmpty"
                :error-messages="'The name field is required.'"
                @keyup="createVehicle.nameEmpty = false"
                />
                <div class="flex w-full gap-x-1">
                    <div class="flex w-1/2 justify-between">
                        <va-select
                        v-model="createVehicle.data.transmission"
                        label="Transmission"
                        requiredMark
                        class="w-full mb-2"
                        :options="auto.gearTypes"
                        text-by="label"
                        value-by="value"
                        />
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-counter
                        v-model="createVehicle.data.capacity"
                        label="Capacity"
                        requiredMark
                        width="100%"
                        class="mb-2"
                        :min="1"
                        :max="10"
                        manual-input
                        />
                    </div>
                </div>
                <va-input
                type="textarea"
                v-model="createVehicle.data.description"
                label="Description"
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(createVehicle.data.description && createVehicle.data.description.length > 0)"
                :max-length=255
                :min-rows="10"
                :max-rows="10"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            createVehicle.data = { ...createVehicle.resetData },
                            createVehicle.nameEmpty = false,
                            createVehicle.saved = false,
                            createVehicle.modal = !createVehicle.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="createVehicle.saved"
                        :disabled="createVehicle.saved"
                        @click="createVehicle.saved = true, insertUpdateVehicle('create')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editVehicle.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Edit Vehicle
                </div>
                <va-input
                v-model="editVehicle.data.name"
                label="Name"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The name field is required.']"
                :error="editVehicle.nameEmpty"
                @keyup="editVehicle.nameEmpty = false"
                />
                <div class="flex w-full gap-x-1">
                    <div class="flex w-1/2 justify-between">
                        <va-select
                        v-model="editVehicle.data.transmission"
                        label="Transmission"
                        requiredMark
                        class="w-full mb-2"
                        :options="auto.gearTypes"
                        text-by="label"
                        value-by="value"
                        />
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-counter
                        v-model="editVehicle.data.capacity"
                        label="Capacity"
                        requiredMark
                        width="100%"
                        class="mb-2"
                        :min="1"
                        :max="10"
                        manual-input
                        />
                    </div>
                </div>
                <va-input
                type="textarea"
                v-model="editVehicle.data.description"
                label="Description"
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(editVehicle.data.description && editVehicle.data.description.length > 0)"
                :max-length=255
                :min-rows="10"
                :max-rows="10"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            editVehicle.data = { ...createVehicle.resetData },
                            editVehicle.nameEmpty = false,
                            editVehicle.saved = false,
                            editVehicle.modal = !editVehicle.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="editVehicle.saved"
                        :disabled="editVehicle.saved"
                        @click="editVehicle.saved = true, insertUpdateVehicle('save')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editVehicle.deleteModal"
    @cancel="editVehicle.data = { ...createVehicle.resetData }"
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Delete Vehicle
                </div>
                <va-alert color="warning">
                    <template #icon>
                        <va-icon name="warning" />
                    </template>
                    This action is irreversible
                </va-alert>
                <va-input
                type="textarea"
                :model-value="editVehicle.data.name"
                class="w-full mb-2 force-noresize"
                readonly
                autosize
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="editVehicle.data = { ...createVehicle.resetData }, editVehicle.deleteModal = !editVehicle.deleteModal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        :loading="editVehicle.saved"
                        :disabled="editVehicle.saved"
                        @click="editVehicle.saved = true, deleteVehicle()"
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

const newVehicle = {
    name: "",
    transmission: 0,
    capacity: 1,
    description: "",
    created_at: formatDate(new Date().getTime(), 'YYYY-MM-DD HH:mm:ss'),
    id: null,
};

export default {
    data () {
        const auto = {
            tblColumns: [
                { key: "name", label: "Vehicle", sortable: true },
                { key: "transmission", label: "Clutch", width: 70, sortable: false },
                { key: "capacity", label: "Capacity", width: 50, sortable: false },
                { key: "description", label: "Description", width: 80, tdAlign: "center", sortable: false },
                { key: "created_at", label: "Created On", width: 125, sortable: true },
                { key: "id", label: "Action", width: 60, sortable: false }
            ],
            gearTypes: [
                { label: "Auto", value: 0, color: "#FFD700" },
                { label: "Manual", value: 1, color: "#C0C0C0" }
            ]
        };

        return {
            auto,
            vehicles: [],
            vehicle: {},
            createVehicle: {
                modal: false,
                nameEmpty: false,
                saved: false,
                resetData: { ...newVehicle },
                data: { ...newVehicle }
            },
            editVehicle: {
                modal: false,
                deleteModal: false,
                nameEmpty: false,
                saved: false,
                data: {}
            },
            activePreviewRow: null,
            filtered: null,
            filter: ""
        };
    },
    computed: {
        pages() {
            return this.$root.config.tblPerPage && this.$root.config.tblPerPage !== 0
            ? Math.ceil(this.filtered.length / this.$root.config.tblPerPage)
            : this.filtered.length;
        },
    },
    mounted () {
        this.getVehicles();
    },
    methods: {
        deleteVehicle() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/vehicle/delete',
                data: { id: this.editVehicle.data.id }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.editVehicle.data = { ...newVehicle };
                    this.editVehicle.deleteModal = false;
                    this.editVehicle.saved = false;

                    this.getVehicles();

                    this.activePreviewRow && this.activePreviewRow.toggleRowDetails(false);
                    this.activePreviewRow = null;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        insertUpdateVehicle(method) {
            if (method !== 'create' || method !== 'save') {
                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/vehicle/save',
                    data: method === 'create' ? this.createVehicle.data : (method === 'save' && this.editVehicle.data)
                }).then(response => {
                    if (response.data.status == 1) {
                        this.$root.prompt(response.data.text);
                        method === 'create' ? (
                            this.createVehicle.data = { ...newVehicle },
                            this.createVehicle.modal = false,
                            this.createVehicle.nameEmpty = false,
                            this.createVehicle.saved = false
                        )
                        : (
                            method === 'save' && (
                                this.editVehicle.data = { ...newVehicle },
                                this.editVehicle.modal = false,
                                this.editVehicle.nameEmpty = false,
                                this.editVehicle.saved = false
                            )
                        );

                        this.getVehicles();
                    } else this.$root.prompt(response.data.text);
                }).catch(error => {
                    // this.$root.prompt(error.response.data.message);
                    let resDataError = Object.keys(error.response.data.errors);

                    if (resDataError.filter(key => key == 'name').length) {
                        method === 'create' ? this.createVehicle.nameEmpty = true
                        : (
                            method === 'save' && (this.editVehicle.nameEmpty = true)
                        );
                    }

                    method === 'create' ? this.createVehicle.saved = false
                    : (method === 'save' && (this.editVehicle.saved = false));
                });
            } else this.$root.prompt();
        },
        getVehicles() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/vehicles'
            }).then(response => {
                if (response.data.status == 1) {
                    this.vehicles = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    }
}
</script>
