<!-- Goodsreview.vue -->
<template>
	<div
		class="ui-review-wrapper ui-detail-common"
		v-if="reviewList.length > 0"
		@click="getCommentStatus()"
	>
		<div class="review-header header"><h3>评价</h3></div>
		<div class="goods-review-body">
			<review-list :list="reviewList"></review-list>
		</div>
	</div>
</template>

<script>
import ReviewList from './ReviewList'
import { getReviewList } from '../../../../api/homegoodsdetail'
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {
      reviewList: []
    }
  },

  computed: {
    ...mapState({
      currentProductId: state => state.goodsdetail.currentProductId
    })
  },

  components: {
    ReviewList
  },

  created () {
    this.getReviewList()
  },

  methods: {
    ...mapMutations({
      changeIndex: 'changeIndex'
    }),

    getReviewList () {
      let params = {
        product: this.currentProductId,
        grade: '',
        page: 1,
        per_page: 10
      }
      getReviewList(params).then(res => {
        if (res) {
          this.reviewList = res.result.goods_eval_list
        }
      })
    },

    /* 评论 */
    getCommentStatus () {
      this.changeIndex(2)
    }
  }
}
</script>

<style lang="scss" scoped>
.ui-review-wrapper {
	height: auto;
	&.ui-detail-common {
		padding: 0;
	}
	.review-header {
		padding: 0 0.75rem;
		height:2.5rem;
		border-bottom: 0.5px solid #e8eaed;
		h3 {
			font-size:0.7rem;
			color: #333;
		}
		img {
			width:0.25rem;
			height:0.5rem;
		}
	}
	.goods-review-body {
		background: #ffffff;
	}
}
</style>
