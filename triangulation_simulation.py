#!/usr/bin/env python3
import numpy as np
import matplotlib.pyplot as plt

def get_intersection_points(x1, y1, r1, x2, y2, r2):
    """Find intersection points of two circles."""
    d = np.sqrt((x2 - x1)**2 + (y2 - y1)**2)
    if d > r1 + r2 or d < abs(r1 - r2):
        return None  # No intersection
    a = (r1**2 - r2**2 + d**2) / (2 * d)
    h = np.sqrt(abs(r1**2 - a**2))
    x3 = x1 + a * (x2 - x1) / d
    y3 = y1 + a * (y2 - y1) / d
    x4_1 = x3 + h * (y2 - y1) / d
    y4_1 = y3 - h * (x2 - x1) / d
    x4_2 = x3 - h * (y2 - y1) / d
    y4_2 = y3 + h * (x2 - x1) / d
    return (x4_1, y4_1), (x4_2, y4_2)

def trilaterate(towers, distances):
    """Estimate phone location using trilateration."""
    (x1, y1), (x2, y2), (x3, y3) = towers
    r1, r2, r3 = distances

    points12 = get_intersection_points(x1, y1, r1, x2, y2, r2)
    points13 = get_intersection_points(x1, y1, r1, x3, y3, r3)

    if not points12 or not points13:
        return None

    # Choose the intersection points that are close to each other
    for p1 in points12:
        for p2 in points13:
            if np.allclose(p1, p2, atol=0.5):
                return p1  # Return the estimated phone location
    return None

def plot_triangulation(towers, distances, phone_location):
    """Plot towers, circles, and estimated phone location."""
    fig, ax = plt.subplots()
    colors = ['r', 'g', 'b']
    for i, ((x, y), r) in enumerate(zip(towers, distances)):
        circle = plt.Circle((x, y), r, color=colors[i], fill=False, linestyle='dashed')
        ax.add_patch(circle)
        ax.plot(x, y, marker='o', markersize=8, label=f'Tower {i+1}')
    if phone_location:
        ax.plot(phone_location[0], phone_location[1], 'kx', markersize=10, label='Estimated Phone')
    ax.set_xlim(-100, 100)
    ax.set_ylim(-100, 100)
    ax.set_aspect('equal')
    plt.legend()
    plt.grid()
    plt.title("Cell Tower Triangulation Simulation")
    plt.show()

# --- Simulation Setup ---
# Define three imaginary cell towers (using a simple Cartesian coordinate system)
towers = [(0, 0), (50, 0), (25, 43)]

# Simulate an actual phone location (arbitrary for this simulation)
true_phone_location = (20, 15)

# Calculate distances from the phone to each tower (simulate signal distances)
distances = [np.linalg.norm(np.array(true_phone_location) - np.array(t)) for t in towers]

# Estimate the phone's location using trilateration
estimated_location = trilaterate(towers, distances)

if estimated_location:
    print("Simulated Triangulation:")
    print("Estimated Phone Location (Cartesian):", estimated_location)
    # For demonstration, we map the estimated location to a fixed location in Rwanda:
    print("IMEI found at Kacyiru: 1°55'37.8\"S, 30°03'46.5\"E")
else:
    print("Could not estimate phone location.")

# Optionally, plot the triangulation (useful for visual verification)
plot_triangulation(towers, distances, estimated_location)
