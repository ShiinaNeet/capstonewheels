<template>
    <div class="mx-5 mb-2 px-2.5 pb-2.5 bg-white rounded">
        <va-data-table
        id="data-table"
        :items="services"
        :columns="serv.tblColumns.filter(item => { return $root.auth.userType === 1 ? (item.key !== 'id') : item })"
        :per-page="$root.config.tblPerPage"
        :current-page="$root.config.tblCurrPage"
        no-data-html="No service(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        >
            <template #headerAppend>
                <tr class="table-crud__slot">
                    <th
                    v-for="key in Object.keys(createService.data).slice(0,7).filter(item => { return $root.auth.userType === 1 ? (item.key !== 'id') : item })"
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
                        v-if="key === 'id' && $root.auth.userType !== 1"
                        preset="primary"
                        icon="post_add"
                        @click="createService.modal = !createService.modal"
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

            <template #cell(type)="{ value }">
                {{ serv.types[value].label }}
            </template>
            <template #cell(duration)="{ value }">
                {{ parseInt(value).toLocaleString() }}
            </template>
            <template #cell(price)="{ value }">
                ₱ {{ parseFloat(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
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
                class="grid gap-y-2 p-2"
                id="table-row-desc"
                >
                    <div class="flex w-full gap-x-2">
                        <div
                        class="w-[150px] justify-between"
                        id="image"
                        >
                            <va-image
                            class="
                                min-w-[150px] max-w-[150px!important]
                                min-h-[150px] max-h-[150px!important]
                                rounded-sm bg-neutral-100
                            "
                            :src="$root.forgeImageFile(rowData.image[0], 'services', false)"
                            :placeholder-src="$root.forgeImageFile(null, null, false)"
                            lazy
                            />
                        </div>
                        <div class="w-[100%]">
                            <va-input
                            type="textarea"
                            :model-value="rowData.description"
                            placeholder="No description available"
                            readonly
                            autosize
                            outline
                            />
                            <div
                            v-if="rowData.requirements"
                            class="mt-2"
                            >
                                <div class="va-title mb-2">
                                    Requirements
                                </div>
                                <va-chip
                                v-for="(req, idx) in rowData.requirements"
                                :key="idx"
                                class="mr-[3px]"
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
                    </div>
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
                @click="
                    editService.data = { ...rowData },
                    editService.data.image =
                        editService.data.image.length > 0 ? (
                            $root.forgeImageFile(editService.data.image, 'services')
                            .then(result => {
                                editService.data.image = result;
                            })
                            .catch(error => {
                                editService.fileNotFound = true;
                                editService.data.image = error;
                            })
                        )
                    : [],
                    editService.modal = !editService.modal
                "/>
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Delete"
                preset="plain"
                icon="delete"
                @click="editService.data = { ...rowData }, editService.deleteModal = !editService.deleteModal"
                />
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(services)">
                    <td
                    id="pagination"
                    :colspan="serv.tblColumns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="$root.config.tblCurrPage"
                            :pages="filter == '' ? $root.tblPagination(services) : (pages, $root.config.tblCurrPage = 1)"
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
    v-model="createService.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Add Service
                </div>
                <va-input
                v-model="createService.data.name"
                label="Name"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The name field is required.']"
                :error="createService.nameEmpty"
                :error-messages="'The name field is required.'"
                @keyup="createService.nameEmpty = false"
                />
                <div class="flex w-full gap-x-1">
                    <div class="flex w-1/4 justify-between">
                        <va-select
                        v-model="createService.data.type"
                        label="Type"
                        requiredMark
                        class="w-full mb-2"
                        :options="serv.types"
                        text-by="label"
                        value-by="value"
                        />
                    </div>
                    <div class="flex w-1/4 justify-between">
                        <va-input
                        v-model="createService.data.price"
                        label="Price"
                        requiredMark
                        class="w-full mb-2"
                        maxlength="10"
                        mask="numeral"
                        :rules="[(v) => v && v.length > 0 || 'Price is required.']"
                        :error="createService.price.empty"
                        :error-messages="createService.price.err === 1 ? 'Limit ≤ 500,000.00' : 'Price is required.'"
                        @keyup="createService.price.empty = false"
                        />
                    </div>
                    <div class="flex w-1/4 justify-between">
                        <va-input
                        v-model="createService.data.duration"
                        label="Duration (Hrs)"
                        requiredMark
                        class="w-full mb-2"
                        maxlength="4"
                        mask="numeral"
                        :rules="[(v) => v && v.length > 0 || 'Duration is required.']"
                        :error="createService.durationEmpty"
                        :error-messages="'Duration is required.'"
                        @keyup="createService.durationEmpty = false"
                        />
                    </div>
                </div>
                <va-select
                v-if="createService.data.type === 0"
                v-model="createService.data.room_id"
                label="Room"
                requiredMark
                class="w-full mb-2"
                :options="rooms"
                text-by="name"
                value-by="id"
                searchable
                highlight-matched-text
                :error="createService.roomIdEmpty"
                :error-messages="'The room field is required.'"
                @update:modelValue="createService.roomIdEmpty = false"
                />
                <va-select
                v-if="createService.data.type === 1"
                v-model="createService.data.vehicle_id"
                label="Vehicle"
                requiredMark
                class="w-full mb-2"
                :options="vehicles"
                text-by="name"
                value-by="id"
                searchable
                highlight-matched-text
                :error="createService.vehicleIdEmpty"
                :error-messages="'The vehicle field is required.'"
                @update:modelValue="createService.vehicleIdEmpty = false"
                />
                <va-select
                v-model="createService.data.requirements"
                label="Requirement(s)"
                requiredMark
                class="w-full mb-2"
                :options="requirements"
                text-by="name"
                value-by="id"
                multiple
                searchable
                clearable
                clearable-icon="backspace"
                highlight-matched-text
                :max-selections="$root.config.requirementPerService"
                :error="createService.requirementsEmpty"
                :error-messages="'The requirement(s) field is required.'"
                @update:modelValue="createService.requirementsEmpty = false"
                >
                    <template #content="{ value }">
                        <va-chip
                        v-for="(req, idx) in value"
                        :key="idx"
                        size="small"
                        color="secondary"
                        class="mr-1 mb-1"
                        square
                        closeable
                        @update:modelValue="updateRequirementsArr('create', idx)"
                        >
                            {{ req.name }}
                        </va-chip>
                    </template>
                </va-select>
                <va-input
                type="textarea"
                v-model="createService.data.description"
                label="Description"
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(createService.data.description.length > 0)"
                :max-length=255
                :min-rows="10"
                :max-rows="10"
                />
                <div>
                    <div class="relative">
                        <label
                        class="va-title absolute left-0 top-0 right-0 px-3.5 pt-[1px] z-[1] text-[#154EC1] tracking-[0.0375rem!important]"
                        aria-hidden="true"
                        >
                            Image
                            <span class="float-right">
                                <va-icon
                                class="mb-[1px]"
                                name="upload"
                                size="11px"
                                />
                                Size Limit: {{ this.$root.config.uploadSizeLimitInMBytes }}MB
                                <va-icon
                                class="mb-[2px]"
                                name="picture_in_picture"
                                size="10px"
                                />
                                Pixels: 150 X 150
                            </span>
                        </label>
                    </div>
                    <va-file-upload
                    v-model="createService.data.image"
                    class="mb-0"
                    file-types=".jpg,.jpeg,.png,image/jpeg,image/png"
                    dropzone
                    dropZoneText="Drop file or click here to upload"
                    :class="!createService.fileOverSizeLimit ? 'valid' : 'error'"
                    :disabled="createService.saved"
                    @update:modelValue="createService.data.image.length > 1 && createService.data.image.shift(), createService.fileOverSizeLimit = false"
                    />
                    <div v-if="createService.fileOverSizeLimit">
                        <div class="va-message-list mt-[2px!important]" style="color: #E42222;">
                            <div class="va-message-list__message">
                                The image exceeds the maximum file size.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            createService.data = { ...createService.resetData },
                            createService.nameEmpty = false,
                            createService.price.empty = false,
                            createService.durationEmpty = false,
                            createService.roomIdEmpty = false,
                            createService.vehicleIdEmpty = false,
                            createService.requirementsEmpty = false,
                            createService.saved = false,
                            createService.modal = !createService.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="createService.saved"
                        :disabled="createService.saved"
                        @click="createService.saved = true, insertUpdateService('create')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model.lazy="editService.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Edit Service
                </div>
                <va-input
                v-model="editService.data.name"
                label="Name"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The name field is required.']"
                :error="editService.nameEmpty"
                :error-messages="'The name field is required.'"
                @keyup="editService.nameEmpty = false"
                />
                <div class="flex w-full gap-x-1">
                    <div class="flex w-1/4 justify-between">
                        <va-select
                        v-model="editService.data.type"
                        label="Type"
                        requiredMark
                        class="w-full mb-2"
                        :options="serv.types"
                        text-by="label"
                        value-by="value"
                        disabled
                        />
                    </div>
                    <div class="flex w-1/4 justify-between">
                        <va-input
                        v-model="editService.data.price"
                        label="Price"
                        requiredMark
                        class="w-full mb-2"
                        maxlength="10"
                        mask="numeral"
                        :rules="[(v) => v && v.length > 0 || 'Price is required.']"
                        :error="editService.price.empty"
                        :error-messages="editService.price.err === 1 ? 'Limit ≤ 500,000.00' : 'Price is required.'"
                        @keyup="editService.price.empty = false"
                        >
                            <template #appendInner>
                                <span>₱</span>
                            </template>
                        </va-input>
                    </div>
                    <div class="flex w-1/4 justify-between">
                        <va-input
                        v-model="editService.data.duration"
                        label="Duration (Hrs)"
                        requiredMark
                        class="w-full mb-2"
                        maxlength="4"
                        mask="numeral"
                        :rules="[(v) => v && v.length > 0 || 'Duration is required.']"
                        :error="editService.durationEmpty"
                        :error-messages="'Duration is required.'"
                        @keyup="editService.durationEmpty = false"
                        />
                    </div>
                </div>
                <va-select
                v-if="editService.data.type === 0"
                v-model="editService.data.room_id"
                label="Room"
                requiredMark
                class="w-full mb-2"
                :options="rooms"
                text-by="name"
                value-by="id"
                searchable
                highlight-matched-text
                :error="editService.roomIdEmpty"
                :error-messages="'The room field is required.'"
                @update:modelValue="editService.roomIdEmpty = false"
                />
                <va-select
                v-if="editService.data.type === 1"
                v-model="editService.data.vehicle_id"
                label="Vehicle"
                requiredMark
                class="w-full mb-2"
                :options="vehicles"
                text-by="name"
                value-by="id"
                searchable
                highlight-matched-text
                :error="editService.vehicleIdEmpty"
                :error-messages="'The vehicle field is required.'"
                @update:modelValue="editService.vehicleIdEmpty = false"
                />
                <va-select
                v-model="editService.data.requirements"
                label="Requirement(s)"
                requiredMark
                class="w-full mb-2"
                :options="requirements"
                text-by="name"
                value-by="id"
                multiple
                searchable
                clearable
                clearable-icon="backspace"
                highlight-matched-text
                :max-selections="$root.config.requirementPerService"
                :error="editService.requirementsEmpty"
                :error-messages="'The requirement(s) field is required.'"
                @update:modelValue="editService.requirementsEmpty = false"
                >
                    <template #content="{ value }">
                        <va-chip
                            v-for="(req, idx) in value"
                            :key="idx"
                            size="small"
                            color="secondary"
                            class="mr-1 mb-1"
                            square
                            closeable
                            @update:modelValue="updateRequirementsArr('save', idx)"
                        >
                            {{ req.name }}
                        </va-chip>
                    </template>
                </va-select>
                <va-input
                type="textarea"
                v-model="editService.data.description"
                label="Description"
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(editService.data.description && editService.data.description.length > 0)"
                :max-length=255
                :min-rows="10"
                :max-rows="10"
                />
                <div>
                    <div class="relative">
                        <label
                        class="va-title absolute left-0 top-0 right-0 px-3.5 pt-[1px] z-[1] text-[#154EC1] tracking-[0.0375rem!important]"
                        aria-hidden="true"
                        >
                            Image
                            <span class="float-right">
                                <va-icon
                                class="mb-[1px]"
                                name="upload"
                                size="11px"
                                />
                                Size Limit: {{ this.$root.config.uploadSizeLimitInMBytes }}MB
                                <va-icon
                                class="mb-[2px]"
                                name="picture_in_picture"
                                size="10px"
                                />
                                Pixels: 150 X 150
                            </span>
                        </label>
                    </div>
                    <va-file-upload
                    v-model="editService.data.image"
                    class="mb-0"
                    file-types=".jpg,.jpeg,.png,image/jpeg,image/png"
                    dropzone
                    dropZoneText="Drop file or click here to upload"
                    :class="!editService.fileOverSizeLimit && !editService.fileNotFound ? 'valid' : 'error'"
                    :disabled="editService.saved"
                    @update:modelValue="
                        editService.data.image.length > 1 && editService.data.image.shift(),
                        editService.fileOverSizeLimit = false,
                        editService.fileNotFound = false
                    "/>
                    <div v-if="editService.fileOverSizeLimit">
                        <div class="va-message-list mt-[2px!important]" style="color: #E42222;">
                            <div class="va-message-list__message">
                                The image exceeds the maximum file size.
                            </div>
                        </div>
                    </div>
                    <div v-if="editService.fileNotFound">
                        <div class="va-message-list mt-[2px!important]" style="color: #E42222;">
                            <div class="va-message-list__message">
                                The image is missing or cannot be retrieved.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            editService.data = { ...createService.resetData },
                            editService.nameEmpty = false,
                            editService.price.empty = false,
                            editService.durationEmpty = false,
                            editService.roomIdEmpty = false,
                            editService.vehicleIdEmpty = false,
                            editService.requirementsEmpty = false,
                            editService.saved = false,
                            editService.modal = !editService.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="editService.saved"
                        :disabled="editService.saved || editService.fileNotFound"
                        @click="editService.saved = true, insertUpdateService('save')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editService.deleteModal"
    @cancel="editService.data = { ...createService.resetData }"
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Delete Requirement
                </div>
                <va-alert color="warning">
                    <template #icon>
                        <va-icon name="warning" />
                    </template>
                    This action is irreversible
                </va-alert>
                <va-input
                type="textarea"
                :model-value="editService.data.name"
                class="w-full mb-2 force-noresize"
                readonly
                autosize
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="editService.data = { ...createService.resetData }, editService.deleteModal = !editService.deleteModal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        @click="deleteService()"
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

const newServ = {
    name: "",
    type: "",
    duration: "",
    price: "",
    description: "",
    created_at: formatDate(new Date().getTime(), 'YYYY-MM-DD HH:mm:ss'),
    id: null,
    type: 0,
    deleted_at: null,
    room_id: null,
    vehicle_id: null,
    image: [],
    requirements: []
};

export default {
    data () {
        const serv = {
            tblColumns: [
                { key: "name", label: "Service", sortable: true },
                { key: "type", label: "Type", width: 80, sortable: false },
                { key: "duration", label: "Duration (Hrs)", sortable: false },
                { key: "price", label: "Price", width: 100, sortable: false },
                { key: "description", label: "Description", width: 80, tdAlign: "center", sortable: false },
                { key: "created_at", label: "Created On", width: 125, sortable: true },
                { key: "id", label: "Action", width: 60, sortable: false }
            ],
            types: [
                { label: "Room", value: 0 },
                { label: "Vehicle", value: 1 }
            ]
        };

        return {
            serv,
            services: [],
            service: {},
            createService: {
                modal: false,
                nameEmpty: false,
                price: {
                    err: 0,
                    empty: false,
                },
                durationEmpty: false,
                roomIdEmpty: false,
                vehicleIdEmpty: false,
                requirementsEmpty: false,
                fileOverSizeLimit: false,
                saved: false,
                resetData: { ...newServ },
                data: { ...newServ }
            },
            editService: {
                modal: false,
                deleteModal: false,
                price: {
                    err: 0,
                    empty: false,
                },
                durationEmpty: false,
                roomIdEmpty: false,
                vehicleIdEmpty: false,
                requirementsEmpty: false,
                fileOverSizeLimit: false,
                fileNotFound: false,
                saved: false,
                data: {}
            },
            activePreviewRow: null,
            rooms: [],
            vehicles: [],
            requirements: [],
            uploadSizeLimitInBytes: this.$root.fileSizeConverterToBytes(this.$root.config.uploadSizeLimitInMBytes),
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
        this.getServices();
    },
    methods: {
        updateRequirementsArr(method, idx) {
            if (method !== 'create' || method !== 'save') {
                method === 'create' ? (
                    this.createService.data.requirements =
                    this.createService.data.requirements.filter(
                        (v) => v !== this.createService.data.requirements[idx]
                    )
                )
                : (
                    method === 'save' && (
                        this.editService.data.requirements =
                        this.editService.data.requirements.filter(
                            (v) => v !== this.editService.data.requirements[idx]
                        )
                    )
                );
            } else this.$root.prompt();
        },
        deleteService() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/service/delete',
                data: { id: this.editService.data.id }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.editService.data = { ...this.createService.resetData };
                    this.editService.deleteModal = false;

                    this.getServices();

                    this.activePreviewRow && this.activePreviewRow.toggleRowDetails(false);
                    this.activePreviewRow = null;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        insertUpdateService(method) {
            if (method !== 'create' || method !== 'save') {
                let fdata, image;
                method === 'create' ? (
                    image = this.createService.data.image,
                    // image = this.createService.data.image, delete this.createService.data.image,
                    this.createService.data.filesize_limit = this.uploadSizeLimitInBytes,
                    fdata = this.createService.data
                    )
                    : (method === 'save') && (
                    image = this.editService.data.image,
                    // image = this.editService.data.image, delete this.editService.data.image,
                    this.editService.data.filesize_limit = this.uploadSizeLimitInBytes,
                    fdata = this.editService.data
                );
                const data = new FormData();
                data.append('form', JSON.stringify(fdata));
                (image.length > 0 && image[0].size !== 0) && data.append('file', image[0]);

                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/service/save',
                    data: data,
                    headers: { 'Content-Type': 'multipart/form-data' }
                }).then(response => {
                    if (response.data.status == 1) {
                        this.$root.prompt(response.data.text);
                        method === 'create' ? (
                            this.createService.data = { ...newServ },
                            this.createService.modal = false,
                            this.createService.nameEmpty = false,
                            this.createService.price.empty = false,
                            this.createService.durationEmpty = false,
                            this.createService.roomIdEmpty = false,
                            this.createService.vehicleIdEmpty = false,
                            this.createService.requirementsEmpty = false,
                            this.createService.fileOverSizeLimit = false,
                            this.createService.saved = false
                        )
                        : (method === 'save' && (
                                this.editService.data = { ...newServ },
                                this.editService.modal = false,
                                this.editService.nameEmpty = false,
                                this.editService.price.empty = false,
                                this.editService.durationEmpty = false,
                                this.editService.roomIdEmpty = false,
                                this.editService.vehicleIdEmpty = false,
                                this.editService.requirementsEmpty = false,
                                this.editService.fileOverSizeLimit = false,
                                this.editService.fileNotFound = false,
                                this.editService.saved = false
                            )
                        );

                        this.getServices();
                    } else this.$root.prompt(response.data.text);
                }).catch(error => {
                    // this.$root.prompt(error.response.data.message);
                    let resDataError = Object.keys(error.response.data.errors);

                    if (resDataError.filter(key => key == 'name').length) {
                        method === 'create' ? this.createService.nameEmpty = true
                        : (
                            method === 'save' && (this.editService.nameEmpty = true)
                        );
                    }
                    if (resDataError.filter(key => key == 'price').length) {
                        method === 'create' ? this.createService.price.empty = true
                        : (
                            method === 'save' && (this.editService.price.empty = true)
                        );

                        error.response.data.errors.price[0].includes('greater than')
                        ? (this.createService.price.err = 1, this.editService.price.err = 1)
                            : (this.createService.price.err = 0, this.editService.price.err = 0);
                    }
                    if (resDataError.filter(key => key == 'duration').length) {
                        method === 'create' ? this.createService.durationEmpty = true
                        : (
                            method === 'save' && (this.editService.durationEmpty = true)
                        );
                    }
                    if (resDataError.filter(key => key == 'room_id').length) {
                        method === 'create' ? this.createService.roomIdEmpty = true
                        : (
                            method === 'save' && (this.editService.roomIdEmpty = true)
                        );
                    }
                    if (resDataError.filter(key => key == 'vehicle_id').length) {
                        method === 'create' ? this.createService.vehicleIdEmpty = true
                        : (
                            method === 'save' && (this.editService.vehicleIdEmpty = true)
                        );
                    }
                    if (resDataError.filter(key => key == 'requirements').length) {
                       method === 'create' ? this.createService.requirementsEmpty = true
                        : (
                            method === 'save' && (this.editService.requirementsEmpty = true)
                        );
                    }
                    if (resDataError.filter(key => key == 'image').length) {
                       method === 'create' ? this.createService.fileOverSizeLimit = true
                        : (
                            method === 'save' && (this.editService.fileOverSizeLimit = true)
                        );
                    }

                    method === 'create' ? this.createService.saved = false
                    : (method === 'save' && (this.editService.saved = false));
                });
            } else this.$root.prompt();
        },
        getServices() {
            (this.rooms.length === 0
                && this.vehicles.length === 0
                    && this.requirements.length === 0) && (
                axios({
                    method: 'GET',
                    type: 'JSON',
                    url: '/service/options'
                }).then(response => {
                    if (response.data.status == 1) {
                        this.rooms = response.data.result.rooms;
                        this.vehicles = response.data.result.vehicles;
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
