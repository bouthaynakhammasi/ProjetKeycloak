export default {
  spec_dir: "spec",
  spec_files: [
    "**/*[sS]pec.?(m)js"
  ],
  helpers: [
    "helpers/**/*.?(m)js"
  ],
  env: {
    stopSpecOnExpectationFailure: false,
    random: true,
    forbidDuplicateNames: true
  },
  // Configure for browser environment
  jsLoader: "import",
  // Add configuration for running in Node.js environment
  browser: false
}
