// $(document).ready(function () {
//     $("input").on("change", function () {
//         $("#commit-button").attr("disabled", false);
//         $("#commit-button").removeClass("btn-secondary");
//         $("#commit-button").addClass("btn-success");
//     });
// });

// $(document).ready(function () {
//     toggleIceOptions();

//     $('select[name="variant"]').on("change", function () {
//         toggleIceOptions();
//     });

//     function toggleIceOptions() {
//         let variant = $('select[name="variant"]').val();

//         if (variant === "Cold") {
//             $('select[name="ice-option"]').show();
//             $('input[name="ice"]').prop("disabled", false);
//             $('input[value="Normal Ice"]').prop("checked", true);
//         } else {
//             $('input[name="ice"]')
//                 .prop("disabled", true)
//                 .prop("checked", false);
//             $('select[name="ice-option"]').hide();
//             // Optional: Tambahkan input hidden untuk nilai "null"
//             if ($("#ice-null").length === 0) {
//                 $("<input>")
//                     .attr({
//                         type: "hidden",
//                         name: "ice",
//                         value: "null",
//                         id: "ice-null",
//                     })
//                     .appendTo("form"); // atau lokasi yang sesuai
//             }
//         }

//         // Hapus input hidden jika varian kembali ke Cold
//         if (variant === "Cold") {
//             $("#ice-null").remove();
//         }
//     }
// });

// $(document).ready(function () {
//     toggleIceOptions();

//     $('select[name="variant"]').on("change", function () {
//         toggleIceOptions();
//     });

//     function toggleIceOptions() {
//         let variant = $('select[name="variant"]').val();

//         if (variant === "Cold") {
//             $('[name="ice-option"]').show(); // ubah ke class atau name sesuai wrapper
//             $('input[name="ice"]').prop("disabled", false);
//             $('input[name="ice"][value="Normal Ice"]').prop("checked", true);

//             // Hapus hidden input "null"
//             $('input[type="hidden"][name="ice"][value="null"]').remove();
//         } else {
//             $('input[name="ice"]')
//                 .prop("disabled", true)
//                 .prop("checked", false);

//             $('[name="ice-option"]').hide(); // ubah ke class atau name sesuai wrapper

//             // Tambahkan hidden input hanya jika belum ada
//             if (
//                 $('input[type="hidden"][name="ice"][value="null"]').length === 0
//             ) {
//                 $("<input>")
//                     .attr({
//                         type: "hidden",
//                         name: "ice",
//                         value: "null",
//                     })
//                     .appendTo("form"); // atau append ke container yang sesuai
//             }
//         }
//     }
// });
