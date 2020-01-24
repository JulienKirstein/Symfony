import json
import numpy as np
from mayavi import mlab

points = json.loads(open("points.txt", "r").read())

x = points["x"]
y = points["y"]
z = points["z"]

x = np.array(x)
y = np.array(y)
z = np.array(z)

mlab.figure(1, fgcolor=(0, 0, 0), bgcolor=(1, 1, 1))

# Visualize the points
pts = mlab.points3d(x, y, z, z, scale_mode='none', scale_factor=0.1)

# Create and visualize the mesh
mesh = mlab.pipeline.delaunay2d(pts)

mq = mlab.pipeline.user_defined(mesh, filter='MeshQuality')
mesh = mlab.pipeline.cell_to_point_data(mq)
mesh = mlab.pipeline.threshold(mesh, up=125)
surf = mlab.pipeline.surface(mesh)

mlab.view(0, 2000, -2500)

mlab.show()
