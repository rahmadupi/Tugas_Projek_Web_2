document.addEventListener("DOMContentLoaded", () => {
  const add_button = document.querySelector(".add-btn");
  const arrange_button = document.getElementById("arrange_button");
  const arrange_container = document.getElementById("arrange_container");
  const section_container = document.getElementById("section_container");
  const activity_modal = document.getElementById("activity_modal");
  const close_modal_button = document.querySelector(".close-btn");
  const save_activity_button = document.getElementById("save_activity_button");
  const activity_input = document.getElementById("activity_input");
  const deadlineInput = document.getElementById("deadlineInput");
  const notesInput = document.getElementById("notesInput");
  const sectionSelect = document.getElementById("sectionSelect");
  const add_section_btn = document.getElementById("add_section_btn");
  const user_id = document.getElementById("user_id").value;
  const username = document.getElementById("username").value;

  let activities = [];
  let sections = [];

  add_button.addEventListener("click", () => {
    activity_modal.style.display = "flex";
    update_section_option();
  });

  close_modal_button.addEventListener("click", () => {
    activity_modal.style.display = "none";
    update_section_option();
  });

  window.addEventListener("click", (event) => {
    if (event.target === activity_modal) {
      activity_modal.style.display = "none";
    }
  });

  save_activity_button.addEventListener("click", () => {
    const activityName = activity_input.value.trim();
    const deadline = deadlineInput.value;
    const sectionId = sectionSelect.value;
    const notes = notesInput.value.trim();
    if (!activityName) {
      alert("Nama aktivitas tidak boleh kosong!");
    } else if (!deadline) {
      alert("Deadline tidak boleh kosong!");
    } else if (!sectionId) {
      alert("Pilih section terlebih dahulu!");
    }
    if (activityName && deadline) {
      const activity = {
        name: activityName,
        deadline: new Date(deadline),
        notes: notes,
        sectionId: sectionId,
      };

      if (username !== "Guest") {
        // Save activity to database
        const formData = new FormData();
        formData.append("name", activityName);
        formData.append("deadline", deadline);
        formData.append("notes", notes);
        formData.append("section_id", sectionId);
        formData.append("user_id", user_id);

        fetch("add_activity.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              render_activity(activity);
              activity_input.value = "";
              deadlineInput.value = "";
              notesInput.value = "";
              activity_modal.style.display = "none";
            } else {
              console.error("Error adding activity:", data.message);
            }
          })
          .catch((error) => console.error("Error adding activity:", error));
        render_activity(activity);
      } else {
        // Save activity to array for guest users
        activities.push(activity);
        render_activity(activity);
        activity_input.value = "";
        deadlineInput.value = "";
        notesInput.value = "";
        activity_modal.style.display = "none";
      }
    } else {
      alert("Beberapa kolom input masih kosong!");
    }
  });

  // edit_save_activity_button.addEventListener("click", () => {
  //   const activityId = edit_activity_id.value;
  //   const activityName = edit_activity_input.value.trim();
  //   const deadline = edit_deadlineInput.value;
  //   const sectionId = edit_sectionSelect.value;
  //   const notes = edit_notesInput.value.trim();
  //   if (!activityName) {
  //     alert("Nama aktivitas tidak boleh kosong!");
  //   } else if (!deadline) {
  //     alert("Deadline tidak boleh kosong!");
  //   } else if (!sectionId) {
  //     alert("Pilih section terlebih dahulu!");
  //   }
  //   if (activityName && deadline) {
  //     const activity = {
  //       id: activityId,
  //       name: activityName,
  //       deadline: new Date(deadline),
  //       notes: notes,
  //       sectionId: sectionId,
  //     };

  //     if (username !== "Guest") {
  //       // Update activity in database
  //       const formData = new FormData();
  //       formData.append("id", activityId);
  //       formData.append("name", activityName);
  //       formData.append("deadline", deadline);
  //       formData.append("notes", notes);
  //       formData.append("section_id", sectionId);

  //       fetch("update_activity.php", {
  //         method: "POST",
  //         body: formData,
  //       })
  //         .then((response) => response.json())
  //         .then((data) => {
  //           if (data.success) {
  //             fetchActivities(); // Refresh the activities list
  //             edit_activity_modal.style.display = "none";
  //           } else {
  //             console.error("Error updating activity:", data.message);
  //           }
  //         })
  //         .catch((error) => console.error("Error updating activity:", error));
  //     } else {
  //       // Update activity in array for guest users
  //       const index = activities.findIndex((a) => a.id === activityId);
  //       if (index !== -1) {
  //         activities[index] = activity;
  //         renderActivity(activity);
  //         edit_activity_modal.style.display = "none";
  //       }
  //     }
  //   } else {
  //     alert("Beberapa kolom input masih kosong!");
  //   }
  // });

  function fetch_activities() {
    fetch("get_activities.php")
      .then((response) => response.json())
      .then((data) => {
        console.log(data); // Log the response for debugging
        if (Array.isArray(data)) {
          activities.length = 0; // Clear existing activities
          section_container
            .querySelectorAll(".activities")
            .forEach((container) => (container.innerHTML = "")); // Clear existing activities in the DOM
          if (data.length === 0) {
            console.log("No activities found.");
            // Optionally, display a message in the UI
            const noActivitiesMessage = document.createElement("p");
            noActivitiesMessage.textContent = "No activities found.";
            section_container.appendChild(noActivitiesMessage);
          } else {
            data.forEach((activity) => {
              activities.push(activity);
              render_activity(activity);
            });
          }
        } else {
          console.error(
            "Error fetching activities: Expected an array but got",
            data
          );
        }
      })
      .catch((error) => console.error("Error fetching activities:", error));
  }

  arrange_button.addEventListener("click", () => {
    arrange_button.classList.toggle("active");
    arrange_container.classList.toggle("hidden");
    const sectionTitles = document.querySelectorAll(".section-title");
    const deleteButtons = document.querySelectorAll(".delete-section-btn");
    // deleteButtons.forEach((button) => button.classList.toggle("hidden"));
    if (arrange_button.classList.contains("active")) {
      console.log("Arrange mode enabled");
      sectionTitles.forEach((title) => {
        title.setAttribute("contenteditable", "true");
        title.addEventListener("input", update_section_option);
        deleteButtons.forEach((button) => button.classList.remove("hidden"));
      });
    } else {
      console.log("Arrange mode disabled");
      sectionTitles.forEach((title) => {
        title.setAttribute("contenteditable", "false");
        title.removeEventListener("input", update_section_option);
        deleteButtons.forEach((button) => button.classList.add("hidden"));
      });
    }
    // deleteButtons.forEach((button) => button.classList.toggle("hidden"));
    update_section_option(); // Update section names in the dropdown list
  });

  function fetch_sections() {
    fetch("get_sections.php")
      .then((response) => response.json())

      .then((data) => {
        sectionSelect.innerHTML = ""; // Clear existing options
        section_container.innerHTML = ""; // Clear existing sections
        data.forEach((section) => {
          const option = document.createElement("option");
          option.value = section.id;
          option.textContent = section.name;
          sectionSelect.appendChild(option);

          const sectionDiv = document.createElement("div");
          sectionDiv.className = "section";
          sectionDiv.id = `${section.id}`;
          sectionDiv.innerHTML = `
                      <div class="section-header">
                          <span class="section-title" contenteditable="true">${section.name}</span>
                          <button class="delete-section-btn">X</button>
                      </div>
                      <div class="activities"></div>
                  `;
          section_container.appendChild(sectionDiv);
        });
        section_title_listeners();
        update_section_option();
      })
      .catch((error) => console.error("Error fetching sections:", error));
  }

  function section_title_listeners() {
    const sectionTitles = document.querySelectorAll(".section-title");
    sectionTitles.forEach((title) => {
      title.addEventListener("input", () => {
        const sectionId = title.closest(".section").id.split("-")[1];
        const newName = title.textContent;

        if (username !== "Guest") {
          const formData = new FormData();
          formData.append("id", sectionId);
          formData.append("name", newName);

          fetch("update_section.php", {
            method: "POST",
            body: formData,
          })
            .then((response) => response.json())
            .then((data) => {
              if (!data.success) {
                console.error("Error updating section:", data.message);
              }
            })
            .catch((error) => console.error("Error updating section:", error));
        } else {
          const section = sections.find((s) => s.id === parseInt(sectionId));
          if (section) {
            section.name = newName;
          }
        }
      });
    });
  }

  add_section_btn.addEventListener("click", () => {
    if (username !== "Guest") {
      console.log(username);
      const sectionName = prompt("Enter section name:");
      if (sectionName) {
        const formData = new FormData();
        formData.append("name", sectionName);

        fetch("add_section.php", { method: "POST", body: formData })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              fetch_sections(); // Refresh the section list
              fetch_activities();
            } else {
              console.error("Error adding section:", data.message);
            }
          })
          .catch((error) => console.error("Error adding section:", error));
      }
    } else {
      const sectionIndex = document.querySelectorAll(".section").length + 1;
      const sectionId = `section-${sectionIndex}`;
      const newSection = document.createElement("div");
      newSection.className = "section";
      newSection.id = sectionId;
      newSection.innerHTML = `
        <div class="section-header">
            <span class="section-title" contenteditable="true">LABEL</span>
            <button class="delete-section-btn">X</button>
        </div>
        <div class="activities"></div>
    `;
      section_container.appendChild(newSection);
      update_section_option();
    }
  });

  section_container.addEventListener("click", (event) => {
    if (event.target.classList.contains("delete-section-btn")) {
      const section = event.target.closest(".section");
      const sectionId = "{$section.id}";

      if (username !== "Guest") {
        // Handle deletion from database
        const formData = new FormData();
        formData.append("id", sectionId);

        fetch("delete_section.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              section_container.removeChild(section);
              updateSectionSelect();
            } else {
              console.error("Error deleting section:", data.message);
            }
          })
          .catch((error) => console.error("Error deleting section:", error));
      } else {
        const sectionId = section.id.split("-")[1];
        sections = sections.filter((s) => s.id !== parseInt(sectionId));
        section_container.removeChild(section);
        update_section_option();
      }
    } else if (event.target.classList.contains("delete-activity-btn")) {
      const activityDiv = event.target.closest(".activity");
      const activityId = activityDiv.id;
      console.log(activityId);

      if (username !== "Guest") {
        // Handle deletion from database
        const formData = new FormData();
        formData.append("id", activityId);

        fetch("delete_activity.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              activities = activities.filter(
                (activity) => activity.id !== parseInt(activityId)
              );
              activityDiv.remove();
            } else {
              console.error("Error deleting activity:", data.message);
            }
          })
          .catch((error) => console.error("Error deleting activity:", error));
      }
      // else if (event.target.classList.contains("edit-activity-btn")) {
      //   const activityDiv = event.target.closest(".activity");
      //   const activityId = activityDiv.dataset.id;
      //   const activity = activities.find((a) => a.id === parseInt(activityId));

      //   if (activity) {
      //     edit_activity_id.value = activity.id;
      //     edit_activity_input.value = activity.name;
      //     edit_deadlineInput.value = activity.deadline.split("T")[0]; // Format date for input
      //     edit_notesInput.value = activity.notes;
      //     edit_sectionSelect.value = activity.section_id;

      //     edit_activity_modal.style.display = "block";
      //   } else {
      //     console.error("Activity not found:", activityId);
      //   }
      // }
      else {
        // Handle deletion from array
        console.log("sss");
        activities = activities.filter(
          (activity) => activity.id !== parseInt(activityId)
        );
        activityDiv.remove();
      }
    }
  });

  // document
  //   .getElementById("edit_save_activity_button")
  //   .addEventListener("submit", function (event) {
  //     event.preventDefault();

  //     const formData = new FormData(this);

  //     fetch("update_activity.php", {
  //       method: "POST",
  //       body: formData,
  //     })
  //       .then((response) => response.json())
  //       .then((data) => {
  //         if (data.success) {
  //           fetchActivities(); // Refresh the activities list
  //           edit_activity_modal.style.display = "none";
  //         } else {
  //           console.error("Error updating activity:", data.message);
  //         }
  //       })
  //       .catch((error) => console.error("Error updating activity:", error));
  //   });

  document.querySelectorAll(".close-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      activity_modal.style.display = "none";
      edit_activity_modal.style.display = "none";
    });
  });

  function update_section_option() {
    sectionSelect.innerHTML = ""; // Clear existing options
    const sections = document.querySelectorAll(".section");
    sections.forEach((section) => {
      const option = document.createElement("option");
      option.value = section.id;
      option.textContent = section.querySelector(".section-title").textContent;
      sectionSelect.appendChild(option);
    });
  }

  function render_activity(activity) {
    const section = document.getElementById(`${activity.label_id}`);
    if (!section) {
      console.error(`Section with ID section-${activity.label_id} not found.`);
      return;
    }
    const activityDiv = document.createElement("div");
    const deadlineDate = new Date(activity.deadline);
    activityDiv.className = "activity";
    activityDiv.id = activity.id;
    activityDiv.innerHTML = `
            <strong>${activity.name}</strong><br>
            Deadline: ${deadlineDate.toLocaleDateString()}<br>
            Notes: ${activity.notes}
            <div class="activity-button">
                
                <button class="delete-activity-btn">Delete</button>
            </div>
        `;
    // <button class="edit-activity-btn">Edit</button>
    const activitiesContainer = section.querySelector(".activities");
    if (!activitiesContainer) {
      console.error(
        `Activities container not found in section-${activity.label_id}.`
      );
      return;
    }
    activitiesContainer.appendChild(activityDiv);
  }

  function check_deadlines() {
    const now = new Date();
    activities.forEach((activity) => {
      if (activity.deadline <= now) {
        alert(`Deadline met for activity: ${activity.name}`);
      }
    });
  }

  setInterval(check_deadlines, 60000);
  if (username !== "Guest") {
    fetch_sections();
    fetch_activities();
  }
});
