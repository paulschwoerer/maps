<template>
  <div class="map-click-popup">
    <h2 class="map-click-popup-title">{{ t("maps", "This Place") }}</h2>

    <textarea
      name="map-click-popup-address"
      class="map-click-popup-address"
      cols="30"
      rows="10"
    ></textarea>

    <Actions>
      <ActionButton
        :title="t('maps', 'Add to favorites')"
        icon="icon-favorite"
      />
      <ActionButton
        :title="t('maps', 'Add contact address')"
        icon="icon-user"
      />
    </Actions>
  </div>
</template>

<script>
import Actions from "@nextcloud/vue/dist/Components/Actions";
import ActionButton from "@nextcloud/vue/dist/Components/ActionButton";

export default {
  name: "MapClickPopup",

  components: {
    ActionButton,
    Actions
  }
};
</script>

<style scoped lang="scss">
.map-click-popup {
  .map-click-popup-title {
  }

  .map-click-popup-address {
    border: 0;
  }
}
</style>
