<template>
  <div class="osm-address">
    <textarea
      :value="textContents"
      @input="$emit('input', $event.target.value)"
      class="osm-address-text"
      cols="30"
      rows="10"
    ></textarea>
  </div>
</template>

<script>
import VueTypes from "vue-types";

export default {
  name: "SimpleOSMAddress",

  props: {
    geocodeObject: VueTypes.shape({
      address: VueTypes.shape({
        country: VueTypes.string,
        county: VueTypes.string,
        country_code: VueTypes.string,
        postcode: VueTypes.string,
        village: VueTypes.string,
        state: VueTypes.string,
        city: VueTypes.string,
        pedestrian: VueTypes.string,
        house_number: VueTypes.string,
        road: VueTypes.string
      }).loose,
      display_name: VueTypes.string,
      lat: VueTypes.string,
      lon: VueTypes.string,
      osm_id: VueTypes.number,
      osm_type: VueTypes.string,
      place_id: VueTypes.number
    }).loose.isRequired
  },

  computed: {
    textContents() {
      const {
        address: {
          country,
          postcode,
          village,
          pedestrian,
          county,
          state,
          city,
          house_number,
          road
        }
      } = this.geocodeObject;

      let address = "";

      if (road) {
        address += `${road} ${house_number || ""}\n`;
      } else if (pedestrian) {
        address += `${pedestrian} ${house_number || ""}\n`;
      }

      if (city) {
        address += `${postcode ? postcode + " " : ""}${city}\n`;
      }

      if (county) {
        address += `${county}\n`;
      }

      if (state) {
        address += `${state}\n`;
      }

      address += country;

      return address;
    }
  }
};
</script>

<style scoped lang="scss">
.osm-address {
  width: 100%;

  .osm-address-text {
    width: 100%;
    max-width: 100%;
    min-width: 100%;
    border: 1px solid rgba(#000, 0.05);
  }
}
</style>
