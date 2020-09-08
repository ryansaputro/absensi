<style lang="scss">
// .pagination {
//     justify-content: flex-end !important;
//     .page-stats {
//         align-items: center;
//         margin-right: 5px;
//     }
//     i {
//         color: #3273dc !important;
//     }
// }
.pagination-next{
    color:#fff;
}
</style>

<template>
    <nav class="pagination" v-if="!client">
        <span class="page-stats">{{pagination.from}} - {{pagination.to}} of {{pagination.total}}</span>
        <a v-if="pagination.prevPageUrl" class="button is-small pagination-previous" @click="$emit('prev');">
            Prev
        </a>
        <a class="button is-small pagination-previous" v-else :disabled="true">
           Prev
        </a>

        <a v-if="pagination.nextPageUrl" class="button is-small pagination-next" @click="$emit('next');">
            Next
        </a>
        <a class="button is-small pagination-next" v-else :disabled="true">
            Next
        </a>
    </nav>

    <nav class="pagination" v-else>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-xs-6 col-sm-6">
                    <span class="page-stats">
                        {{pagination.from}} - {{pagination.to}} of {{filtered.length}}
                        <span v-if="filtered.length < pagination.total">(filtered from {{pagination.total}} total entries)</span>
                    </span>
                </div>
                <div class="col-md-6 col-xs-6 col-sm-6 text-right">
                    <a v-if="pagination.prevPage" class="btn btn-xs btn-primary button is-small pagination-previous" @click="$emit('prev');">
                        Prev
                    </a>
                    <a class="btn btn-xs btn-primary button is-small pagination-previous" v-else :disabled="true">
                    Prev
                    </a>

                    <a v-if="pagination.nextPage" class="btn btn-xs btn-primary button is-small pagination-next" @click="$emit('next');">
                        Next
                    </a>
                    <a class="btn btn-xs btn-primary button is-small pagination-next" v-else :disabled="true">
                        Next
                    </a>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    export default {
        props: ['pagination', 'client', 'filtered']
    }
</script>