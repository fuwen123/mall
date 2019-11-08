<template>
    <div class="container">
            <div v-if="favorites_list && favorites_list.length">
                <mt-cell-swipe class="store-item"  v-for="(item, index) in favorites_list" v-bind:key="item.fav_id" :right="rightBottom(item.fav_id)">
                    <div @click="$router.push({'name': 'HomeStoredetail', 'query': {'id': item.store_id}})">
                        <div class="p-img">
                            <img class="collection-img" v-bind:src="item.store_avatar_url"/>
                        </div>
                    <div class="flex-right">
                        <span class="title ml-10">{{ item.store_name }}</span>
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
import { getFavoriteStoreList, delFavoriteStore } from '../../../api/memberFavorite'
export default {
  components: {
    EmptyRecord
  },
  name: 'MemberFavoriteStore',
  data () {
    return {
      favorites_list: false
    }
  },
  created: function () {
    this.getFavoriteStoreList()
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
      delFavoriteStore(favId).then(res => {
        if (res) {
          this.getFavoriteStoreList()
        }
      })
    },
    getFavoriteStoreList () {
      Indicator.open()
      getFavoriteStoreList(this.page, {}).then(res => {
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
.store-item{height:3rem;width:100%;}
.store-item .p-img{float:left;padding:0.25rem 0;margin-left:0.5rem;}
.store-item .p-img img{width:2.5rem;height:2.5rem;border-radius:50%}
.flex-right {float:left;}
.flex-right span{line-height:3rem;width:8rem;height:3rem;overflow:hidden;display:block;float:left}
</style>

<!--Cell Swipe样式覆盖 -->
<style>
    .store-item .mint-cell-swipe-button {
        width: 4.5rem;
        font-size: 0.7rem;
        display: flex !important;
        justify-content: center;
        align-items: center;
        box-sizing: border-box;
    }
    .store-item .mint-cell-wrapper {
        padding: 0;
    }
    .store-item .mint-cell-wrapper .mint-cell-value {
        width: 100%;
        border-bottom: 1px solid rgba(232, 234, 237, 1);
    }
</style>
