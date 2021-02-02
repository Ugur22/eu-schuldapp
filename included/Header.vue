<template>
    <nb-header :style="styles.background">
      <nb-left  :style="styles.position">
        <nb-button transparent :on-press="goBack"  >
          <nb-icon :style="styles.icon"  :name="Platform.OS === 'android' ? 'arrow-back' : 'arrow-round-back'" />
        </nb-button>
      </nb-left>
      <nb-body  :style="styles.position">
        <nb-title :style="styles.textHeader">{{pageTitle}}</nb-title>
      </nb-body>
      <nb-right :style="styles.position">
        <nb-button transparent>
          <nb-icon :style="styles.icon" name="information-circle" />
        </nb-button>
      </nb-right>
    </nb-header>
</template>
<script>
import {styles} from '../pages/styling/style';
import { Platform } from "react-native";

  export default {
	props: {
	  activeBtn: {
		type: String
      },
      method: {type: Function},
      pageTitle:''
	},
	data() {
	  return {
      styles,
      Platform
	  };
	},
	methods: {
    goBack: function(){
        this.method();
      },
	  }
  }
</script>