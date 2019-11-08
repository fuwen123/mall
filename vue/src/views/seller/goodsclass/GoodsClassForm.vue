<template>
	<div class="container">
		<div class="common-header-wrap">
			<mt-header :title="getTitle" class="common-header">
				<mt-button slot="left" icon="back" @click="goBack"></mt-button>
			</mt-header>
		</div>

		<mt-field label="分类名称" v-model="class_info.storegc_name" ></mt-field>
		<div @click="parentVisible=true">
			<mt-cell title="上级分类" :value="parent_name" is-link />
		</div>
		<mt-field label="排序" v-model="class_info.storegc_sort" ></mt-field>

		<mt-cell title="显示" ><mt-switch v-model="state"></mt-switch></mt-cell>
		<mt-button class="ds-button-large" type="primary"  v-on:click="submit">保存</mt-button>

		<mt-popup v-model="parentVisible" position="right" class="common-popup-wrapper">
			<div class="common-header-wrap">
				<mt-header title="上级分类" class="common-header">
					<mt-button slot="left" icon="back" @click="parentVisible=false"></mt-button>
				</mt-header>
			</div>
			<div class="common-popup-content">
				<mt-radio
						v-model="parent_id"
						:options="class_options">
				</mt-radio>
			</div>
		</mt-popup>
	</div>
</template>

<script>
import { Toast, Indicator } from 'mint-ui'
import { getCommonData, getGoodsClassInfo, editGoodsClass } from '../../../api/sellerGoodsClass'
export default {
  components: {
  },
  computed: {
    isAddMode () {
      let mode = this.$route.query.action
      // add: 添加地址，edit: 编辑地址
      if (mode === 'add') {
        return true
      } else {
        return false
      }
    },
    getTitle () {
      if (this.isAddMode) {
        return '新增分类'
      } else {
        return '修改分类'
      }
    }

  },
  data () {
    return {
      parent_name: '无',
      state: true,
      parentVisible: false,
      class_info: {
        storegc_id: 0,
        storegc_sort: 255
      },
      parent_id: '0',
      class_options: [{
        label: '无',
        value: '0'
      }],
      parent_list: {
        '0': '无'
      }
    }
  },
  created: function () {
    getCommonData().then(res => {
      let temp = res.result.goods_class
      for (var i in temp) {
        this.parent_list[temp[i].storegc_id + ''] = temp[i].storegc_name
        this.class_options.push({
          label: temp[i].storegc_name,
          value: temp[i].storegc_id + ''
        })
      }
    }).catch(function (error) {
      Toast(error.message)
    })
    if (!this.isAddMode) {
      getGoodsClassInfo(this.$route.query.storegc_id).then(res => {
        this.class_info = res.result.class_info
        this.parent_id = res.result.class_info.storegc_parent_id + ''
        if (this.class_info.storegc_state) {
          this.state = true
        } else {
          this.state = false
        }
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  },
  watch: {

    parent_id: function (parent_id) {
      this.parent_name = this.parent_list[parent_id]
      this.parentVisible = false
    }

  },
  methods: {
    goBack () {
      this.$router.go(-1)
    },
    submit () {
      if (!this.class_info.storegc_name) {
        Toast('请填写分类名称')
        return
      }
      if (this.state) {
        this.class_info.storegc_state = 1
      } else {
        this.class_info.storegc_state = 0
      }
      this.class_info.storegc_parent_id = this.parent_id
      editGoodsClass(this.class_info).then(
        (response) => {
          this.$router.go(-1)
        }, (error) => {
          Toast(error.message)
        })
    }

  }
}
</script>

<style lang="scss" scoped>
	.right-arrow{transform: rotate(-90deg);color:#ddd;font-size: .6rem;display: inline-block;}
	.input-wrap{position: relative;
		i{position: absolute;right:0;top:0;line-height: 2.4rem;display: block;width:2rem;text-align: center;font-size: 1rem}
	}
</style>
