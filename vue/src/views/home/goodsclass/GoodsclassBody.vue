<template>
  <div class="ui-category-body">
    <div class="category-flex">
      <div class="category-sidebar">
        <ul>
          <li class="item"
              v-for='item in items'
              v-bind:key="item.id"
              v-on:click='(item.children && item.children.length)?onItemClick(item):goProduct(item.id)'
              v-bind:class="{'sidbaractive': (currentItem && item.id == currentItem.id), 'noActive' : (currentItem && item.id != currentItem.id)}">
            <a>{{ item.value }}</a>
          </li>
        </ul>
      </div>
      <div class="category-content" v-if='currentItem && currentItem.children'>
        <ul>
          <li class="item clearfix" v-for='item in currentItem.children' v-bind:key = "item.id">
            <a v-on:click='goProduct(item.id)'>{{item.value}}</a>
            <dl v-for='item_1 in item.children' :key = "item_1.id" v-on:click='goProduct(item_1.id)'>
              <dt><img :src="getGoodsclassImg(item_1.id)" :onerror="errorImg" /></dt>
              <dd>{{item_1.value}}</dd>
            </dl>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { Indicator, Toast } from 'mint-ui'
import { mapState, mapMutations, mapActions } from 'vuex'
export default {
  data () {
    return {
      errorImg: 'this.src="' + require('../../../assets/image/no_image.jpg') + '"'
    }
  },
  computed: {
    ...mapState({
      items: state => state.goodsclass.items,
      currentItem: state => state.goodsclass.currentItem
    }),
    getGoodsclassImg: function () {
      return function (id) {
        return process.env.VUE_APP_SITE_URL + '/uploads/home/common/category-pic-' + id + '.jpg'
      }
    }
  },
  created () {
    this.getGoodsclassList()
  },
  methods: {
    ...mapMutations({
      saveCurrentGoodsclassItem: 'saveCurrentGoodsclassItem'
    }),
    ...mapActions({
      fetchGoodsclassList: 'fetchGoodsclassList'
    }),
    getGoodsclassList () {
      if (!(this.items && this.items.length)) {
        Indicator.open()
      }
      this.fetchGoodsclassList().then((response) => {
        Indicator.close()
      }, (error) => {
        Toast(error.message)
        Indicator.close()
      })
    },
    onItemClick (item) {
      this.saveCurrentGoodsclassItem(item)
    },
    goProduct (id) {
      let params = { 'cate_id': id }
      this.$router.push({ 'name': 'HomeGoodslist', 'query': params })
    }
  }
}
</script>

<style scoped lang="scss">
  .ui-category-body{
    width: 100%;
    .category-flex {
      display: flex;
      display: -webkit-box;
      display: -moz-box;
      display: -ms-flexbox;
      display: -webkit-flex;
      width: 100%;
      position: absolute;
      bottom: 0;
      width: 100%;
      top: 2.5rem;
      .category-sidebar {
        flex-basis:6rem;
        background-color: #F0F2F5;
        overflow-y: scroll;
        ul {
          border-right: 0.5px solid #E8EAED;
          li {
            display: block;
            padding: 0.8rem 0.25rem;
            a {
              color: #333;
              display: -webkit-box;
              -webkit-box-orient: vertical;
              -webkit-line-clamp: 1;
              overflow: hidden;
              font-size:0.6rem;
              text-align: center;
            }
          }
          li.noActive {
            background-color: #F0F2F5;
            border-left: 0.1rem solid transparent;
            a {
              color: #333;
            }
          }
          li.sidbaractive{
            background-color: #FFFFFF;
            border-left: 0.1rem solid #e93b3d;
            a {
              font-weight:600;
              color: $mainColor;
            }
          }
        }
      }
      .category-content {
        width: 100%;
        background-color: #fff;
        overflow: auto;
        margin-bottom:3rem;
        ul {
          height: 100%;
          li {
            display: block;
            padding: 0.95rem 0;
            text-align: center;
            cursor: pointer;
            border-bottom: 0.5px solid rgb(232,234,237);
            a {
              color: #333;
              font-weight:700;
              line-height:2rem;
              font-size:0.7rem;
              text-align: center;
              display:block;;
            }
          }
          dl{
            display:inline-block;
            width:32.8%;
            text-align:center;
            dt{
              position:relative;
              img{width:3.5rem;height:3.5rem;overflow:hidden;}
            }
            dd{font-size:0.6rem;line-height:1rem;height:1rem;overflow: hidden;margin-bottom:0.5rem;}
          }
        }
      }
    }
  }
</style>
