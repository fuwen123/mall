<template>
	<div class="container">
		<div v-if="favorites_list && favorites_list.length">
			<mt-cell-swipe class="goods-item"  v-for="(item, index) in favorites_list" v-bind:key="item.fav_id" :right="rightBottom(item.fav_id)">
				<div @click="$router.push({'name': 'HomeGoodsdetail', 'query': {'goods_id': item.goods_id}})">
					<div class="p-img">
						<img class="collection-img" v-bind:src="item.goods_image_url"/>
					</div>
					<div class="flex-right">
						<span class="title ml-10">{{ item.goods_name }}</span>
					</div>
				</div>
			</mt-cell-swipe>
		</div>
		<empty-record v-else-if="favorites_list && !favorites_list.length"></empty-record>
	</div>
</template>
<script>
import { Toast, Indicator, MessageBox } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getFavoriteGoodsList, delFavoriteGoods } from '../../../api/memberFavorite'
export default {
  components: {
    EmptyRecord
  },
  name: 'MemberFavoriteGoods',
  data () {
    return {
      favorites_list: false
    }
  },
  created: function () {
    this.getFavoriteGoodsList()
  },

  methods: {
    // 左滑事件
    rightBottom (favId) {
      return [
        {
          content: '删除',
          style: { background: 'red', color: '#fff' },
          handler: () =>
            MessageBox({
              title: '确认删除',
              message: '是否要取消关注此店铺？',
              showCancelButton: true
            }).then(action => {
              if (action === 'confirm') {
                this.getCancelCollection(favId)
              }
            })
        }
      ]
    },
    // 取消收藏店铺数据
    getCancelCollection (favId) {
      delFavoriteGoods(favId).then(res => {
        if (res) {
          this.getFavoriteGoodsList()
        }
      })
    },
    getFavoriteGoodsList () {
      Indicator.open()
      getFavoriteGoodsList(this.page, {}).then(res => {
        Indicator.close()
        this.favorites_list = res.result.favorites_list
      }).catch(function (error) {
        Indicator.close()
        Toast(error.message)
      })
    }
  }
}
</script>
<style lang="scss" scoped>
	.goods-item{height:3rem;width:100%;}
	.goods-item .p-img{float:left;padding:0.25rem 0;margin-left:0.5rem;}
	.goods-item .p-img img{width:2.5rem;height:2.5rem;border-radius:50%}
	.flex-right {float:left;}
	.flex-right span{line-height:3rem;width:8rem;height:3rem;overflow:hidden;display:block;float:left}
</style>

<!--Cell Swipe样式覆盖 -->
<style>
	.goods-item .mint-cell-swipe-button {
		width: 4.5rem;
		font-size: 0.7rem;
		display: flex !important;
		justify-content: center;
		align-items: center;
		box-sizing: border-box;
	}
	.goods-item .mint-cell-wrapper {
		padding: 0;
	}
	.goods-item .mint-cell-wrapper .mint-cell-value {
		width: 100%;
		border-bottom: 1px solid rgba(232, 234, 237, 1);
	}
</style>
